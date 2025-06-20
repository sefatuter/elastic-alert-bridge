<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    private $elastalertPath;
    private $venvPath;       
    private $pidFile;        
    private $logFile;     
    private $configFile;    
    private $elastalertExecutable;

    public function __construct()
    {
        $baseUserPath = storage_path('app/elastalert2');

        $this->elastalertPath = rtrim($baseUserPath, '/');
        $this->venvPath = $this->elastalertPath . '/venv';
        $this->pidFile = $this->elastalertPath . '/logs/elastalert.pid';
        $this->logFile = $this->elastalertPath . '/logs/elastalert.log';
        $this->configFile = $this->elastalertPath . '/config.yaml';
        $this->elastalertExecutable = $this->venvPath . '/bin/elastalert';
    }

    public function getStatus()
    {
        $status = 'stopped';
        $pid = null;
        $pidFileExists = file_exists($this->pidFile);
        
        if ($pidFileExists) {
            $pid = trim(file_get_contents($this->pidFile));
            if ($pid && $this->isProcessRunning($pid)) {
                $status = 'running';
            } else {
                if ($pid) {
                    Log::info("Stale PID file found ({$this->pidFile}) for PID {$pid}. Process not running. Removing PID file.");
                    unlink($this->pidFile);
                } else if ($pidFileExists) { // PID file exists but is empty or unreadable
                    Log::warning("PID file ({$this->pidFile}) exists but is empty or unreadable. Removing.");
                    unlink($this->pidFile);
                }
                $status = 'stopped';
                $pid = null; // Reset pid as it's not valid
            }
        }
        
        return response()->json([
            'status' => $status,
            'pid' => $pid
        ]);
    }

    public function start(Request $request)
    {
        if ($this->isElastAlertRunning()) {
            return redirect()->back()->with('error', 'ElastAlert is already running.');
        }

        try {
            // Validate paths
            if (!is_dir($this->elastalertPath)) {
                $msg = 'ElastAlert application directory not found: ' . $this->elastalertPath;
                Log::error($msg);
                return redirect()->back()->with('error', $msg);
            }
            if (!is_dir($this->venvPath)) {
                $msg = 'Python virtual environment not found: ' . $this->venvPath;
                Log::error($msg);
                return redirect()->back()->with('error', $msg);
            }
            if (!file_exists($this->configFile)) {
                $msg = 'ElastAlert config file not found: ' . $this->configFile;
                Log::error($msg);
                return redirect()->back()->with('error', $msg);
            }
            if (!file_exists($this->elastalertExecutable)) {
                $msg = 'ElastAlert executable not found: ' . $this->elastalertExecutable;
                Log::error($msg);
                return redirect()->back()->with('error', $msg);
            }
            if (!is_executable($this->elastalertExecutable)) {
                $msg = 'ElastAlert executable is not executable: ' . $this->elastalertExecutable . '. Check permissions.';
                Log::error($msg);
                return redirect()->back()->with('error', $msg);
            }

            // Check writability for PID and Log file directories
            $pidDir = dirname($this->pidFile);
            if (!is_writable($pidDir)) {
                $msg = "PID file directory ('{$pidDir}') is not writable by the web server user.";
                Log::error($msg);
                return redirect()->back()->with('error', $msg);
            }
            $logDir = dirname($this->logFile);
            if (!is_writable($logDir)) {
                $msg = "Log file directory ('{$logDir}') is not writable by the web server user.";
                Log::error($msg);
                return redirect()->back()->with('error', $msg);
            }

            // Clean up any old PID file if the process isn't actually running
            if (file_exists($this->pidFile)) {
                $oldPid = trim(file_get_contents($this->pidFile));
                if ($oldPid && !$this->isProcessRunning($oldPid)) {
                    unlink($this->pidFile);
                }
            }
            
            // Clear previous log file content for this attempt
            file_put_contents($this->logFile, ''); // Requires write permission for the file itself or dir if creating

            // Command to start ElastAlert
            // Using full paths and ensuring the script is run in its directory context
            // The `cd` is important for ElastAlert to find relative paths in its config (e.g., for rules_folder)
            $command = "cd {$this->elastalertPath} && {$this->elastalertExecutable} --verbose --config {$this->configFile} > {$this->logFile} 2>&1 & echo $! > {$this->pidFile}";
            
            Log::info("Attempting to start ElastAlert with command: " . $command);
            shell_exec($command);
            
            sleep(5);

            if ($this->isElastAlertRunning()) {
                Log::info('ElastAlert started successfully. PID: ' . trim(file_get_contents($this->pidFile)));
                return redirect()->back()->with('success', 'ElastAlert started successfully.');
            } else {
                $errorDetails = "Failed to start ElastAlert.";
                if (file_exists($this->logFile)) {
                    $logContent = file_get_contents($this->logFile);
                    if (!empty(trim($logContent))) {
                        $errorDetails .= " Log (last 500 chars): " . substr(trim($logContent), -500);
                    } else {
                        $errorDetails .= " Log file ({$this->logFile}) is empty. This could be due to permissions issues preventing ElastAlert from running/writing logs, or an immediate crash. Check web server user permissions for execute on '{$this->elastalertExecutable}' and write on '{$this->logFile}' and '{$this->pidFile}'. Also, verify '{$this->configFile}'.";
                    }
                } else {
                    $errorDetails .= " Log file ({$this->logFile}) was not created. Check permissions.";
                }
                if (file_exists($this->pidFile)) {
                    $pidContent = trim(file_get_contents($this->pidFile));
                    $errorDetails .= " PID file ({$this->pidFile}) content: '{$pidContent}'. Process may have exited.";
                     // If PID file has a value, but process isn't running, it means it died.
                    if ($pidContent && !$this->isProcessRunning($pidContent)) {
                        unlink($this->pidFile); // Clean up misleading PID file
                    }
                } else {
                     $errorDetails .= " PID file ({$this->pidFile}) was not created.";
                }
                Log::error($errorDetails . " Command attempted: " . $command);
                return redirect()->back()->with('error', $errorDetails);
            }
        } catch (\Exception $e) {
            Log::error('ElastAlert start exception: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error starting ElastAlert: ' . $e->getMessage());
        }
    }

    public function stop(Request $request)
    {
        if (!file_exists($this->pidFile)) {
            return redirect()->back()->with('error', 'ElastAlert is not running (PID file not found).');
        }

        $pid = trim(file_get_contents($this->pidFile));

        if (!$pid || !$this->isProcessRunning($pid)) {
             if (file_exists($this->pidFile)) {
                unlink($this->pidFile); // Stale PID file
            }
            return redirect()->back()->with('error', 'ElastAlert is not running (process not found for PID: ' . $pid . ').');
        }

        try {
            Log::info("Attempting to stop ElastAlert (PID: {$pid}) with SIGTERM.");
            shell_exec("kill -TERM {$pid}");
            sleep(3); // Give it time to shut down gracefully
            
            if ($this->isProcessRunning($pid)) {
                Log::warning("ElastAlert (PID: {$pid}) did not stop with SIGTERM. Sending SIGKILL.");
                shell_exec("kill -9 {$pid}");
                sleep(1); // Give it time to die
            }
            
            if ($this->isProcessRunning($pid)) {
                Log::error("Failed to stop ElastAlert (PID: {$pid}) even with SIGKILL.");
                return redirect()->back()->with('error', 'Failed to stop ElastAlert (PID: ' . $pid . '). Manual intervention might be required.');
            }

            if (file_exists($this->pidFile)) {
                unlink($this->pidFile);
            }
            
            Log::info('ElastAlert stopped successfully.');
            return redirect()->back()->with('success', 'ElastAlert stopped successfully.');
        } catch (\Exception $e) {
            Log::error('ElastAlert stop error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error stopping ElastAlert: ' . $e->getMessage());
        }
    }

    public function restart(Request $request)
    {
        Log::info('Attempting to restart ElastAlert.');
        try {
            if ($this->isElastAlertRunning()) {
                $stopResponse = $this->stop($request); // This will redirect
                // Check if stop was successful; if not, the redirect from stop() will occur
                if (session('error')) { 
                    // Stop failed, no need to attempt start. stop() already set the message.
                    return $stopResponse;
                }
                // If stop was successful, it redirects back. To avoid double redirect,
                // we need to manage the flow carefully or make stop() return a boolean.
                // For simplicity now, assume if no error, it's proceeding.
                Log::info('ElastAlert stopped as part of restart. Proceeding to start.');
                sleep(3); // Wait a bit after stop before starting again
            }
            
            // Call start. This will also redirect.
            return $this->start($request);

        } catch (\Exception $e) {
            Log::error('ElastAlert restart error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error restarting ElastAlert: ' . $e->getMessage());
        }
    }

    private function isElastAlertRunning()
    {
        if (!file_exists($this->pidFile)) {
            return false;
        }
        
        $pid = trim(file_get_contents($this->pidFile));
        if (!$pid) { // PID file is empty
            unlink($this->pidFile); // Clean up empty PID file
            return false;
        }
        
        if ($this->isProcessRunning($pid)) {
            return true;
        } else {
            // Process not running, so PID file is stale
            Log::info("isElastAlertRunning: Stale PID file found for PID {$pid}. Process not running. Removing PID file: {$this->pidFile}");
            unlink($this->pidFile);
            return false;
        }
    }

    private function isProcessRunning($pid)
    {
        if (empty($pid) || !ctype_digit((string)$pid)) {
            return false;
        }
        // `ps -p PID -o pid=` returns the PID if process exists, empty otherwise.
        // `2>/dev/null` suppresses errors if PID doesn't exist.
        $result = shell_exec("ps -p {$pid} -o pid= 2>/dev/null");
        return !empty(trim($result));
    }

    public function getLogs(Request $request)
    {
        try {
            // Get the number of lines to return (default: 100, max: 1000)
            $lines = min((int) $request->get('lines', 100), 1000);
            
            if (!file_exists($this->logFile)) {
                return response()->json([
                    'logs' => '',
                    'error' => 'Log file not found. ElastAlert may not have been started yet.'
                ]);
            }

            // Read the last N lines from the log file
            $logs = $this->getTailLines($this->logFile, $lines);
            
            return response()->json([
                'logs' => $logs,
                'timestamp' => time()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to read ElastAlert logs: ' . $e->getMessage());
            return response()->json([
                'logs' => '',
                'error' => 'Failed to read logs: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getTailLines($file, $lines)
    {
        $handle = fopen($file, 'r');
        if (!$handle) {
            return '';
        }

        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = array();

        while ($linecounter > 0) {
            $t = " ";
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines - $linecounter - 1] = fgets($handle);
            if ($beginning) break;
        }
        fclose($handle);
        return implode("", array_reverse($text));
    }
}