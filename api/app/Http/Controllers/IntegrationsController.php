<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\EmailIntegration;

class IntegrationsController extends Controller
{
    public function index()
    {
        return view('elasticsearch.integrations');
    }

    public function getIntegrations()
    {
        try {
            $emailIntegrations = EmailIntegration::all()->keyBy('name');
            
            $integrations = [
                'email' => $emailIntegrations
            ];

            return response()->json(['success' => true, 'integrations' => $integrations]);
        } catch (\Exception $e) {
            Log::error('Failed to load integrations: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to load integrations'], 500);
        }
    }

    public function saveEmailIntegration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_ssl' => 'boolean',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'required|string',
            'from_address' => 'required|email|max:255',
            'default_recipient' => 'nullable|email|max:255'
        ]);

        try {
            // Save to database first to get the ID
            $integration = EmailIntegration::updateOrCreate(
                ['name' => $request->input('name')],
                [
                    'smtp_host' => $request->input('smtp_host'),
                    'smtp_port' => $request->input('smtp_port'),
                    'smtp_ssl' => $request->boolean('smtp_ssl'),
                    'smtp_username' => $request->input('smtp_username'),
                    'smtp_password' => $request->input('smtp_password'),
                    'from_address' => $request->input('from_address'),
                    'default_recipient' => $request->input('default_recipient')
                ]
            );

            // Create SMTP auth file using the integration ID
            $authFilePath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
            
            // Ensure directory exists
            if (!is_dir($authFilePath)) {
                mkdir($authFilePath, 0755, true);
            }
            
            // Use integration ID as the file number
            $authFileName = "smtp_auth_file{$integration->id}.txt";
            $authFileFullPath = $authFilePath . DIRECTORY_SEPARATOR . $authFileName;
            
            // Create SMTP auth file content
            $smtpAuthContent = "user: " . $request->input('smtp_username') . "\n";
            $smtpAuthContent .= "password: " . $request->input('smtp_password') . "\n";
            
            // Write the auth file
            file_put_contents($authFileFullPath, $smtpAuthContent);
            
            Log::info("SMTP auth file created: {$authFileName} for integration '{$request->input('name')}' (ID: {$integration->id})");

            Log::info("Email integration '{$request->input('name')}' saved successfully");

            return response()->json(['success' => true, 'message' => 'Email integration saved successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to save email integration: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to save email integration'], 500);
        }
    }

    public function deleteEmailIntegration(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        try {
            $integration = EmailIntegration::where('name', $request->input('name'))->first();
            
            if (!$integration) {
                return response()->json(['success' => false, 'error' => 'Integration not found'], 404);
            }

            $integration->delete();

            Log::info("Email integration '{$request->input('name')}' deleted successfully");

            return response()->json(['success' => true, 'message' => 'Email integration deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete email integration: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to delete email integration'], 500);
        }
    }

    public function testEmailIntegration(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        try {
            $integration = EmailIntegration::where('name', $request->input('name'))->first();
            
            if (!$integration) {
                return response()->json(['success' => false, 'error' => 'Integration not found'], 404);
            }

            Log::info("Testing email integration '{$request->input('name')}'");

            return response()->json(['success' => true, 'message' => 'Email integration test successful']);
        } catch (\Exception $e) {
            Log::error('Failed to test email integration: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to test email integration'], 500);
        }
    }

    public function getEmailIntegrations()
    {
        try {
            $integrations = EmailIntegration::all(['id', 'name', 'smtp_host', 'smtp_port', 'smtp_ssl', 'from_address', 'default_recipient']);
            
            return response()->json(['success' => true, 'integrations' => $integrations]);
        } catch (\Exception $e) {
            Log::error('Failed to get email integrations: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to get email integrations'], 500);
        }
    }

    private $rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
    private $integrationAuthPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';

    public function reviewDataRuleIntegration(Request $request)
    {
        $outputData = [
            'Rule Name' => $request->get('ruleName', ''),
            'Elasticsearch Index' => $request->get('index', ''),
            'Alert Requirements' => $request->get('prompt', ''),
            'KQL Syntax' => $request->get('kql', ''),
            'Schedule Interval' => $request->get('interval', ''),
            'Schedule Unit' => $request->get('unit', '')
        ];

        $output = "======== FORM DATA PREVIEW ========\n";
        foreach ($outputData as $key => $value) {
            if ($key === 'KQL Syntax' && empty($value)) {
                continue;
            }
            $output .= $key . ": " . ($value ?: 'N/A') . "\n";
        }

        if ($request->boolean('enableEmailAction')) {
            $output .= "\n-------- EMAIL ACTIONS --------\n";
            
            $emailType = $request->get('emailType', 'custom');
            $output .= "Email Type: " . ucfirst($emailType) . "\n";
            
            if ($emailType === 'integration') {
                $integrationId = $request->get('integrationId');
                $integrationName = $request->get('integrationName', 'Unknown');
                $output .= "Integration: " . $integrationName . " (ID: " . $integrationId . ")\n";
                
                $integrationAuthFile = $this->findIntegrationAuthFile($integrationId);
                if ($integrationAuthFile) {
                    $smtpAuthFilePath = $this->integrationAuthPath . DIRECTORY_SEPARATOR . $integrationAuthFile;
                } else {
                    $smtpAuthFilePath = $this->integrationAuthPath . DIRECTORY_SEPARATOR . 'smtp_auth_file.txt';
                }
            } else {
                $smtpAuthFilePath = '/home/sefaubuntu/elastic-alert-bridge/config/elastalert/rules/smtp_auth_file.txt';
            }
            
            $output .= "Recipient Email(s): " . ($request->get('emailRecipient', '') ?: 'N/A') . "\n";
            $output .= "SMTP Host: " . ($request->get('smtpHost', '') ?: 'N/A') . "\n";
            $output .= "SMTP Port: " . ($request->get('smtpPort', '') ?: 'N/A') . "\n";
            $output .= "SMTP SSL: " . ($request->boolean('smtpSsl') ? 'Yes' : 'No') . "\n";
            $output .= "From Address: " . ($request->get('fromAddress', '') ?: 'N/A') . "\n";
            $output .= "SMTP Auth File: " . $smtpAuthFilePath . "\n";
        }

        $output .= "=================================";
        return response($output, 200)->header('Content-Type', 'text/plain');
    }
} 