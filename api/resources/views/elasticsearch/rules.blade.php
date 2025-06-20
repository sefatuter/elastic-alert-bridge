<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElastAlert Rules</title>
    <style>
        body {
            font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f7f9fb;
            color: #343741;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .rules-header {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .rules-title {
            font-size: 20px;
            font-weight: 500;
            margin-left: 16px;
        }
        .back-button {
            background: #006bb4;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-button:hover {
            background: #005a9e;
        }
        .rules-container {
            display: flex;
            flex-grow: 1;
            overflow: hidden; /* Prevent body scroll, allow internal scroll */
        }
        .rules-sidebar {
            width: 280px;
            background: #ffffff;
            border-right: 1px solid #d3dae6;
            padding: 20px;
            overflow-y: auto;
        }
        .rules-sidebar h3 {
            font-size: 16px;
            font-weight: 600;
            color: #343741;
            margin-top: 0;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f2f5;
        }
        .rules-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .rules-list-item a {
            display: block;
            padding: 10px 12px;
            text-decoration: none;
            color: #343741;
            border-radius: 4px;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .rules-list-item a:hover {
            background-color: #f0f2f5;
            color: #006bb4;
        }
        .rules-list-item a.selected {
            background-color: #e7f3ff;
            color: #006bb4;
            font-weight: 600;
        }
        .rules-content-area {
            flex-grow: 1;
            padding: 24px;
            background-color: #ffffff;
            overflow-y: auto;
        }
        .rules-content-area pre {
            background-color: #f7f9fb;
            border: 1px solid #d3dae6;
            padding: 16px;
            border-radius: 6px;
            white-space: pre-wrap; /* Handles long lines */
            word-wrap: break-word; /* Ensures words break to fit */
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            margin: 0;
        }
        .no-rule-selected {
            color: #69707d;
            font-size: 16px;
            text-align: center;
            margin-top: 40px;
        }
        .control-panel {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .control-left {
            flex-grow: 1;
        }
        .control-right {
            flex-grow: 1;
            text-align: right;
        }
        .control-buttons {
            display: inline-flex;
            align-items: center;
            gap: 16px;
        }
        .control-btn {
            background: #006bb4;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
        }
        .control-btn:hover {
            background: #005a9e;
        }
        
        .start-btn {
            background: #28a745;
        }
        
        .start-btn:hover {
            background: #218838;
        }
        
        .stop-btn {
            background: #dc3545;
        }
        
        .stop-btn:hover {
            background: #c82333;
        }
        
        .restart-btn {
            background: #17a2b8;
        }
        
        .restart-btn:hover {
            background: #138496;
        }
        
        .status-display {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-label {
            font-weight: 600;
        }
        
        .status-indicator {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .status-running {
            background: #d4edda;
            color: #155724;
        }
        
        .status-stopped {
            background: #f8d7da;
            color: #721c24;
        }
        .message {
            padding: 16px 24px;
            margin: 0;
            border-radius: 0;
            font-weight: 500;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            border-bottom: 1px solid #c3e6cb;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border-bottom: 1px solid #f5c6cb;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .control-middle {
            flex-grow: 2;
            padding: 0 24px;
        }
        .logs-section {
            background: #f7f9fb;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            height: 120px;
            display: flex;
            flex-direction: column;
            position: relative;
            min-height: 80px;
            max-height: 400px;
        }
        .logs-header {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: between;
            border-radius: 6px 6px 0 0;
        }
        .logs-title {
            font-size: 14px;
            font-weight: 600;
            color: #343741;
            flex-grow: 1;
        }
        .logs-content {
            flex-grow: 1;
            padding: 8px 12px;
            overflow-y: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 11px;
            line-height: 1.3;
            background: #fafbfc;
            white-space: pre-wrap;
            word-wrap: break-word;
            color: #343741;
        }
        .logs-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logs-auto-refresh {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
        }
        .logs-clear {
            background: #dc3545;
            color: #ffffff;
            border: none;
            border-radius: 3px;
            padding: 4px 8px;
            font-size: 11px;
            cursor: pointer;
        }
        .logs-clear:hover {
            background: #c82333;
        }
        .logs-resize-handle {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: transparent;
            cursor: ns-resize;
            border-radius: 0 0 6px 6px;
        }
        .logs-resize-handle:hover {
            background: #006bb4;
            opacity: 0.3;
        }
        .logs-resize-handle::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 2px;
            background: #d3dae6;
            border-radius: 1px;
        }
        .logs-resize-handle:hover::after {
            background: #006bb4;
        }
    </style>
</head>
<body>
    <div class="rules-header">
        <a href="{{ route('elasticsearch.index') }}" class="back-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/></svg>
            Back
        </a>
        <h1 class="rules-title">ElastAlert Rules</h1>
    </div>

    <!-- ElastAlert Control Panel -->
    <div class="control-panel">
        <div class="control-left">
            <h3>ElastAlert Control</h3>
            <div class="control-buttons">
                <form method="POST" action="{{ route('elastalert.start') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="control-btn start-btn">Start</button>
                </form>
                <form method="POST" action="{{ route('elastalert.stop') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="control-btn stop-btn">Stop</button>
                </form>
                <form method="POST" action="{{ route('elastalert.restart') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="control-btn restart-btn">Restart</button>
                </form>
            </div>
        </div>
        <div class="control-middle">
            <div class="logs-section">
                <div class="logs-header">
                    <span class="logs-title">ElastAlert Logs</span>
                    <div class="logs-controls">
                        <label class="logs-auto-refresh">
                            <input type="checkbox" id="autoRefreshLogs" checked> Auto-refresh
                        </label>
                        <button class="logs-clear" onclick="clearLogs()">Clear</button>
                    </div>
                </div>
                <div class="logs-content" id="logsContent">Loading logs...</div>
                <div class="logs-resize-handle" id="logsResizeHandle"></div>
            </div>
        </div>
        <div class="control-right">
            <div class="status-display">
                <span class="status-label">Status:</span>
                <span id="statusIndicator" class="status-indicator">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="message success-message">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="message error-message">
            {{ session('error') }}
        </div>
    @endif

    <div class="rules-container">
        <div class="rules-sidebar">
            <h3>Rule Files</h3>
            <ul class="rules-list" id="rulesList">
                {{-- Rule files will be listed here by JavaScript --}}
                <li class="no-rule-selected" style="text-align:left; padding:10px 0;">Loading rules...</li>
            </ul>
        </div>
        <div class="rules-content-area">
            <pre id="ruleContentDisplay"><p class="no-rule-selected">Select a rule file from the left to view its content.</p></pre>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rulesListUl = document.getElementById('rulesList');
            const ruleContentDisplayPre = document.getElementById('ruleContentDisplay');
            let currentSelectedFileElement = null;

            async function loadRuleFiles() {
                rulesListUl.innerHTML = '<li class="no-rule-selected" style="text-align:left; padding:10px 0;">Loading rules...</li>'; // Set loading message upfront

                try {
                    const response = await fetch('{{ route("api.elasticsearch.rules.list") }}');
                    
                    let filesData;
                    // Try to parse JSON regardless of response.ok, as error responses from our controller are JSON
                    try {
                        filesData = await response.json();
                    } catch (jsonError) {
                        console.error('JSON parsing error:', jsonError);
                        const responseText = await response.text().catch(() => 'Could not read response text.'); 
                        throw new Error(`Failed to parse server response. Status: ${response.status}. Server sent: ${responseText.substring(0,200)}...`);
                    }

                    if (!response.ok) {
                        let errorMsg = filesData.error || `HTTP error! Status: ${response.status}`;
                        if (filesData.path) {
                            errorMsg += ` (Path: ${filesData.path})`;
                        }
                        throw new Error(errorMsg);
                    }
                    
                    rulesListUl.innerHTML = ''; // Clear loading message

                    // filesData should now be the actual array of filenames or an object with an error key handled by the !response.ok block above
                    if (!Array.isArray(filesData)) {
                        // This case should ideally be caught by !response.ok if the controller sends an error object.
                        // But as a fallback if filesData is an object without an error key from a 200 OK response.
                        console.error('Expected an array of files, but received:', filesData);
                        rulesListUl.innerHTML = `<li class="no-rule-selected" style="color:red; text-align:left; padding:10px 0;">Error: Received invalid data format from server.</li>`;
                        return;
                    }

                    if (filesData.length === 0) {
                        rulesListUl.innerHTML = '<li class="no-rule-selected" style="text-align:left; padding:10px 0;">No rule files found in the directory.</li>';
                        return;
                    }

                    filesData.forEach(fileName => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('rules-list-item');
                        const link = document.createElement('a');
                        link.href = '#';
                        link.textContent = fileName;
                        link.dataset.fileName = fileName;
                        link.addEventListener('click', async function(e) {
                            e.preventDefault();
                            await loadRuleContent(fileName, this);
                        });
                        listItem.appendChild(link);
                        rulesListUl.appendChild(listItem);
                    });

                    const urlParams = new URLSearchParams(window.location.search);
                    const selectedFileFromUrl = urlParams.get('selected_file');
                    if (selectedFileFromUrl) {
                        const linkToSelect = rulesListUl.querySelector(`a[data-file-name="${selectedFileFromUrl}"]`);
                        if (linkToSelect) {
                            await loadRuleContent(selectedFileFromUrl, linkToSelect);
                        } else {
                            console.warn(`Selected file '${selectedFileFromUrl}' from URL not found in the list.`);
                        }
                    }

                } catch (error) { 
                    console.error('Failed to load rule files (outer catch):', error);
                    rulesListUl.innerHTML = `<li class="no-rule-selected" style="color:red; text-align:left; padding:10px 0;">Failed to load rules: ${error.message}. Check console.</li>`;
                }
            }

            async function loadRuleContent(fileName, linkElement) {
                let displayElement = ruleContentDisplayPre;
                if (!displayElement) { 
                    const contentArea = document.querySelector('.rules-content-area');
                    if (contentArea) {
                        displayElement = document.createElement('pre'); // Ensure it's a pre for consistency
                        displayElement.id = 'ruleContentDisplay'; // Give it the id if creating
                        contentArea.innerHTML = ''; 
                        contentArea.appendChild(displayElement);
                    } else {
                        console.error('.rules-content-area not found!');
                        alert('Error: Content display area not found.');
                        return;
                    }
                }
                displayElement.textContent = 'Loading content...';
                
                if (currentSelectedFileElement) {
                    currentSelectedFileElement.classList.remove('selected');
                }
                linkElement.classList.add('selected');
                currentSelectedFileElement = linkElement;

                try {
                    const response = await fetch(`{{ route("api.elasticsearch.rules.content") }}?file=${encodeURIComponent(fileName)}`);
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({error: response.statusText}) ); // Try to parse error, fallback
                        throw new Error(`HTTP error! status: ${response.status}. ${errorData.error || 'Failed to fetch content.'}`);
                    }
                    const content = await response.text();
                    displayElement.textContent = content;
                } catch (error) {
                    console.error(`Failed to load content for ${fileName}:`, error);
                    displayElement.textContent = `Error loading ${fileName}:\n${error.message}`
                }
            }

            loadRuleFiles();
            
            // ElastAlert Status Checking
            function updateStatus() {
                const statusIndicator = document.getElementById('statusIndicator');
                
                fetch('{{ route("api.elastalert.status") }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        statusIndicator.className = 'status-indicator';
                        if (data.status === 'running') {
                            statusIndicator.classList.add('status-running');
                            statusIndicator.textContent = 'Running';
                            if (data.pid) {
                                statusIndicator.title = `PID: ${data.pid}`;
                            }
                        } else {
                            statusIndicator.classList.add('status-stopped');
                            statusIndicator.textContent = 'Stopped';
                            statusIndicator.title = '';
                        }
                    })
                    .catch(error => {
                        console.error('Status check failed:', error);
                        statusIndicator.className = 'status-indicator';
                        statusIndicator.textContent = 'Error';
                        statusIndicator.title = `Error: ${error.message}`;
                    });
            }
            
            // Check status on page load
            updateStatus();
            
            // Check status every 30 seconds
            setInterval(updateStatus, 30000);

            // Logs functionality
            let logsInterval = null;

            window.clearLogs = function() {
                document.getElementById('logsContent').textContent = '';
            };

            function loadLogs() {
                const logsContent = document.getElementById('logsContent');
                
                fetch('{{ route("api.elastalert.logs") }}?lines=50')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            logsContent.textContent = `Error: ${data.error}`;
                        } else if (data.logs) {
                            logsContent.textContent = data.logs;
                            // Auto-scroll to bottom
                            logsContent.scrollTop = logsContent.scrollHeight;
                        } else {
                            logsContent.textContent = 'No logs available';
                        }
                    })
                    .catch(error => {
                        console.error('Failed to load logs:', error);
                        logsContent.textContent = `Failed to load logs: ${error.message}`;
                    });
            }

            function startAutoRefresh() {
                const autoRefreshCheckbox = document.getElementById('autoRefreshLogs');
                
                if (logsInterval) {
                    clearInterval(logsInterval);
                }
                
                if (autoRefreshCheckbox.checked) {
                    logsInterval = setInterval(loadLogs, 5000); // Refresh every 5 seconds
                }
            }

            // Handle auto-refresh checkbox change
            document.getElementById('autoRefreshLogs').addEventListener('change', function() {
                startAutoRefresh();
            });

            // Load logs on page load
            loadLogs();
            startAutoRefresh();

            // Resize functionality
            let isResizing = false;
            let startY = 0;
            let startHeight = 0;

            const logsSection = document.querySelector('.logs-section');
            const resizeHandle = document.getElementById('logsResizeHandle');

            resizeHandle.addEventListener('mousedown', function(e) {
                isResizing = true;
                startY = e.clientY;
                startHeight = parseInt(window.getComputedStyle(logsSection).height, 10);
                document.addEventListener('mousemove', handleResize);
                document.addEventListener('mouseup', stopResize);
                e.preventDefault();
            });

            function handleResize(e) {
                if (!isResizing) return;
                
                const height = startHeight + (e.clientY - startY);
                const minHeight = 80;
                const maxHeight = 400;
                
                if (height >= minHeight && height <= maxHeight) {
                    logsSection.style.height = height + 'px';
                }
            }

            function stopResize() {
                isResizing = false;
                document.removeEventListener('mousemove', handleResize);
                document.removeEventListener('mouseup', stopResize);
            }
        });
    </script>
</body>
</html>