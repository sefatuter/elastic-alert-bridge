<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Alert Rule - Elasticsearch</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
        
        .alert-header {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .alert-title {
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

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 24px;
            flex-grow: 1;
            overflow-y: auto;
        }


        .form-section {
            background: #ffffff;
            border: 1px solid #d3dae6;
            border-radius: 8px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #343741;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f2f5;
        }

        .form-group {
            margin-bottom: 28px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #343741;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-description {
            font-size: 13px;
            color: #69707d;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            background: #ffffff;
            transition: all 0.2s ease;
            line-height: 1.4;
        }

        .form-input:focus {
            outline: none;
            border-color: #006bb4;
            box-shadow: 0 0 0 3px rgba(0, 107, 180, 0.1);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
            font-family: inherit;
        }

        .prompt-textarea {
            min-height: 140px;
            font-size: 15px;
            line-height: 1.5;
        }

        .kql-textarea {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            min-height: 100px;
        }

        .schedule-section {
            background: #f7f9fb;
            border: 1px solid #d3dae6;
            border-radius: 8px;
            padding: 24px;
        }

        .schedule-row {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            margin-top: 16px;
        }

        .schedule-input {
            width: 80px;
            padding: 10px 12px;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
            background: #ffffff;
        }

        .schedule-select {
            padding: 10px 14px;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            font-size: 14px;
            background: #ffffff;
            min-width: 120px;
            cursor: pointer;
        }

        .schedule-select:focus {
            outline: none;
            border-color: #006bb4;
            box-shadow: 0 0 0 3px rgba(0, 107, 180, 0.1);
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #d3dae6;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 40px;
        }

        .btn-secondary {
            background: #ffffff;
            color: #343741;
            border-color: #d3dae6;
        }

        .btn-secondary:hover {
            background: #f7f9fb;
            border-color: #98a2b3;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:active {
            background: #f0f2f5;
            transform: translateY(1px);
        }

        .btn-primary {
            background: #006bb4;
            color: #ffffff;
            border-color: #006bb4;
            box-shadow: 0 1px 3px rgba(0, 107, 180, 0.3);
        }

        .btn-primary:hover {
            background: #005a9e;
            border-color: #005a9e;
            box-shadow: 0 2px 6px rgba(0, 107, 180, 0.4);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            background: #004a87;
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0, 107, 180, 0.3);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            margin-right: 8px;
        }

        .icon-arrow-left {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z'/%3E%3C/svg%3E");
        }

        .helper-text {
            font-size: 12px;
            color: #69707d;
            margin-top: 6px;
            line-height: 1.4;
        }

        .optional-badge {
            display: inline-block;
            background: #f0f2f5;
            color: #69707d;
            font-size: 12px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="alert-header">
        <a href="{{ route('elasticsearch.index') }}" class="back-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/></svg>
            Back
        </a>
        <h1 class="alert-title">Create ElastAlert Rule</h1>
    </div>
    <div class="container">

        <!-- Main Form -->
        <div class="form-section">
            <h2 class="section-title">Rule Configuration</h2>
            
            <form id="alertForm">
                <!-- Rule Name Field -->
                <div class="form-group">
                    <label class="form-label" for="ruleName">Rule Name</label>
                    <div class="form-description">
                        Give your alert rule a descriptive name that helps identify its purpose.
                    </div>
                    <input 
                        type="text" 
                        id="ruleName" 
                        class="form-input" 
                        placeholder="e.g., Payment Service Error Alert, High Response Time Monitor..."
                        required>
                    <div class="helper-text">
                        Use a clear, descriptive name that explains what this rule monitors.
                    </div>
                </div>

                <!-- Index Selection Field -->
                <div class="form-group">
                    <label class="form-label" for="indexSelect">Elasticsearch Index</label>
                    <div class="form-description">
                        Select the Elasticsearch index that contains the data you want to monitor.
                    </div>
                    <select 
                        id="indexSelect" 
                        class="form-input" 
                        required>
                        <option value="">Select an index...</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                    <div class="helper-text">
                        Choose the index where your log data is stored. If you don't see your index, make sure it exists and contains data.
                    </div>
                </div>

                <!-- Prompt Field -->
                <div class="form-group">
                    <label class="form-label" for="alertPrompt">Describe your alert requirements</label>
                    <div class="form-description">
                        Explain what conditions should trigger an alert. Be specific about log patterns, error types, or system behaviors you want to monitor.
                    </div>
                    <textarea 
                        id="alertPrompt" 
                        class="form-input form-textarea prompt-textarea" 
                        placeholder="Example: Alert me when there are more than 10 error logs in the last 5 minutes from the payment service, or when the response time exceeds 2 seconds..."
                        required></textarea>
                    <div class="helper-text">
                        The more detailed your description, the better the AI can generate an accurate ElastAlert rule.
                    </div>
                </div>

                <!-- Optional KQL Syntax -->
                <div class="form-group">
                    <label class="form-label" for="kqlSyntax">
                        KQL Syntax 
                        <span class="optional-badge">Optional</span>
                    </label>
                    <div class="form-description">
                        Provide specific Kibana Query Language syntax if you have preferred query patterns.
                    </div>
                    <textarea 
                        id="kqlSyntax" 
                        class="form-input form-textarea kql-textarea" 
                        placeholder="level: error AND service.name: payment"
                        ></textarea>
                    <div class="helper-text">
                        Examples: <code>status: 500</code>, <code>message: "timeout" AND host.name: web-*</code>, <code>response_time > 2000</code>
                    </div>
                </div>
            </form>
        </div>

        <!-- Rule Schedule Section -->
        <div class="form-section schedule-section">
            <h2 class="section-title">Rule Schedule</h2>
            <div class="form-description" style="margin-bottom: 20px;">
                Set the frequency to check the alert conditions
            </div>
            
            <div class="schedule-row">
                <span>Check every</span>
                <input type="number" value="5" class="schedule-input" min="1" max="1440" id="scheduleInterval">
                <select class="schedule-select" id="scheduleUnit">
                    <option value="minutes">minutes</option>
                    <option value="hours">hours</option>
                    <option value="days">days</option>
                </select>
            </div>
            
            <div class="helper-text" style="margin-top: 12px;">
                More frequent checks provide faster alerts but consume more resources. Recommended: 1-5 minutes for critical alerts.
            </div>
        </div>

        <!-- Actions Section -->
        <div class="form-section">
            <h2 class="section-title">Actions</h2>
            <div class="form-description" style="margin-bottom: 20px;">
                Configure actions to be performed when an alert is triggered.
            </div>

            <div class="form-group">
                <label class="form-label" style="display: flex; align-items: center;">
                    <input type="checkbox" id="enableEmailAction" onchange="toggleEmailActionFields()" style="margin-right: 10px; width: 16px; height: 16px;">
                    Send Email Notification
                </label>
            </div>

            <div id="emailActionFields" style="display: none; padding-left: 26px; border-left: 2px solid #f0f2f5; margin-top: 20px;">
                <div class="form-group">
                    <label class="form-label" for="emailRecipient">Recipient Email(s)</label>
                    <div class="form-description">Enter one or more email addresses, separated by commas.</div>
                    <input type="email" id="emailRecipient" class="form-input" placeholder="e.g., user1@example.com, user2@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="smtpHost">SMTP Host</label>
                    <input type="text" id="smtpHost" class="form-input" placeholder="e.g., smtp.gmail.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="smtpPort">SMTP Port</label>
                    <input type="number" id="smtpPort" class="form-input" placeholder="e.g., 465 or 587">
                </div>

                <div class="form-group">
                    <label class="form-label" style="display: flex; align-items: center;">
                        <input type="checkbox" id="smtpSsl" style="margin-right: 10px; width: 16px; height: 16px;" checked>
                        Use SSL for SMTP
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label" for="fromAddress">From Address</label>
                    <input type="email" id="fromAddress" class="form-input" placeholder="e.g., alerts@yourdomain.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="smtpUsername">SMTP Username (Email)</label>
                    <input type="email" id="smtpUsername" class="form-input" placeholder="Usually your email address">
                </div>

                <div class="form-group">
                    <label class="form-label" for="smtpPassword">SMTP Password</label>
                    <input type="password" id="smtpPassword" class="form-input">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="btn btn-secondary" onclick="goBack()">
                Cancel
            </button>
            <button type="button" class="btn btn-primary" onclick="generateRule()">
                Generate Rule
            </button>
            <button type="button" class="btn btn-primary" onclick="printRule()">
                Print Rule
            </button>
        </div>

        <!-- Placeholder for printed rule -->
        <div id="printedRuleOutputContainer" style="margin-top: 24px; padding: 16px; background: #f0f2f5; border: 1px solid #d3dae6; border-radius: 8px; display: none;">
            <h3 style="margin-bottom: 12px; font-size: 16px; font-weight: 600;">Form Data Preview:</h3>
            <pre id="printedRuleOutput" style="white-space: pre-wrap; word-wrap: break-word; font-family: monospace, monospace; font-size: 13px;"></pre>
        </div>
    </div>

    <script>
        // Auto-resize textareas
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // Set up auto-resize for all textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                autoResize(this);
            });
            
            // Initial resize
            autoResize(textarea);
        });

        // Load available indexes
        async function loadIndexes() {
            try {
                const response = await fetch('/api/elasticsearch/indexes');
                const indexes = await response.json();
                
                const indexSelect = document.getElementById('indexSelect');
                
                // Clear existing options except the first one
                indexSelect.innerHTML = '<option value="">Select an index...</option>';
                
                // Add index options
                indexes.forEach(index => {
                    const option = document.createElement('option');
                    option.value = index.display_name; // Set value to display_name
                    option.textContent = `${index.display_name} (${index.docs_count.toLocaleString()} docs)`;
                    // Store the actual index name (which could be the data stream display name or original name)
                    option.dataset.backingIndex = index.name; 
                    indexSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Failed to load indexes:', error);
                // Add a fallback option
                const indexSelect = document.getElementById('indexSelect');
                indexSelect.innerHTML = '<option value="">Unable to load indexes</option>';
            }
        }

        // Form validation
        function validateForm() {
            const ruleName = document.getElementById('ruleName').value.trim();
            const selectedIndex = document.getElementById('indexSelect').value;
            const prompt = document.getElementById('alertPrompt').value.trim();
            
            if (!ruleName) {
                alert('Please enter a name for your alert rule.');
                return false;
            }
            
            if (ruleName.length < 3) {
                alert('Rule name must be at least 3 characters long.');
                return false;
            }
            
            if (!selectedIndex) {
                alert('Please select an Elasticsearch index to monitor.');
                return false;
            }
            
            if (!prompt) {
                alert('Please describe your alert requirements.');
                return false;
            }
            
            if (prompt.length < 10) {
                alert('Please provide a more detailed description of your alert requirements.');
                return false;
            }
            
            return true;
        }

        // Generate rule function
        function generateRule() {
            console.log('Generate rule button clicked');
            
            if (!validateForm()) {
                return;
            }

            // Get form data
            const indexSelect = document.getElementById('indexSelect');

            const formData = {
                ruleName: document.getElementById('ruleName').value.trim(),
                index: indexSelect.value, // Display name
                prompt: document.getElementById('alertPrompt').value.trim(), 
                kql: document.getElementById('kqlSyntax').value.trim(),
                interval: document.getElementById('scheduleInterval').value,
                unit: document.getElementById('scheduleUnit').value
            };

            // Add email action data if enabled
            const enableEmailAction = document.getElementById('enableEmailAction').checked;
            formData.enableEmailAction = enableEmailAction;
            if (enableEmailAction) {
                formData.emailRecipient = document.getElementById('emailRecipient').value.trim();
                formData.smtpHost = document.getElementById('smtpHost').value.trim();
                formData.smtpPort = document.getElementById('smtpPort').value;
                formData.smtpSsl = document.getElementById('smtpSsl').checked;
                formData.fromAddress = document.getElementById('fromAddress').value.trim();
                formData.smtpUsername = document.getElementById('smtpUsername').value.trim();
                formData.smtpPassword = document.getElementById('smtpPassword').value; 
            }
            
            console.log('Form data for generateRule (POST request):', formData);
            
            const generateBtn = document.querySelector('button[onclick="generateRule()"]'); // More specific selector
            const originalText = generateBtn.textContent;
            generateBtn.textContent = 'Generating...';
            generateBtn.disabled = true;
            
            // Changed to POST request with JSON body
            fetch('/elasticsearch/generate-rule', { // URL directly used, ensure it matches route
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(result => {
                console.log('Response data:', result);
                if (result.success && result.redirect_url) {
                    alert('Success! ' + (result.message || 'Rule generated. Redirecting to rules page...'));
                    window.location.href = result.redirect_url;
                } else {
                    alert('Error: ' + (result.error || 'An unknown error occurred.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error occurred. Check console for details.');
            })
            .finally(() => {
                // Reset button state
                generateBtn.textContent = originalText;
                generateBtn.disabled = false;
            });
        }

        // Form submission handler
        document.getElementById('alertForm').addEventListener('submit', function(e) {
            e.preventDefault();
            generateRule();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                generateRule();
            }
        });

        // Go back function
        function goBack() {
            // Try to go back in history first
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
            } else {
                // If no history or referrer, redirect to main elasticsearch page
                window.location.href = '/elasticsearch';
            }
        }

        // Print rule function
        async function printRule() {
            console.log('Print rule button clicked');
            
            // Get form data (similar to generateRule)
            const indexSelect = document.getElementById('indexSelect');
            const selectedOption = indexSelect.options[indexSelect.selectedIndex];
            const backingIndex = selectedOption.dataset.backingIndex || indexSelect.value;

            const formData = {
                ruleName: document.getElementById('ruleName').value.trim(),
                index: indexSelect.value,
                backingIndex: backingIndex,
                prompt: document.getElementById('alertPrompt').value.trim(),
                kql: document.getElementById('kqlSyntax').value.trim(),
                interval: document.getElementById('scheduleInterval').value,
                unit: document.getElementById('scheduleUnit').value
            };

            // Add email action data if enabled
            const enableEmailAction = document.getElementById('enableEmailAction').checked;
            formData.enableEmailAction = enableEmailAction;
            if (enableEmailAction) {
                formData.emailRecipient = document.getElementById('emailRecipient').value.trim();
                formData.smtpHost = document.getElementById('smtpHost').value.trim();
                formData.smtpPort = document.getElementById('smtpPort').value;
                formData.smtpSsl = document.getElementById('smtpSsl').checked;
                formData.fromAddress = document.getElementById('fromAddress').value.trim();
                formData.smtpUsername = document.getElementById('smtpUsername').value.trim();
                formData.smtpPassword = document.getElementById('smtpPassword').value; // Password not trimmed
            }
            
            console.log('Form data for print:', formData);
            
            const printBtn = event.target;
            const originalPrintBtnText = printBtn.textContent;
            printBtn.textContent = 'Printing...';
            printBtn.disabled = true;

            const outputContainer = document.getElementById('printedRuleOutputContainer');
            const outputPre = document.getElementById('printedRuleOutput');
            outputPre.textContent = 'Loading...';
            outputContainer.style.display = 'block';
            
            // Build query string
            const params = new URLSearchParams(formData);
            const url = '/elasticsearch/print-rule?' + params.toString();
            
            console.log('Making request to:', url);
            
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const textData = await response.text();
                outputPre.textContent = textData;
            } catch (error) {
                console.error('Error printing rule:', error);
                outputPre.textContent = 'Error printing rule. Check console for details.';
                alert('Error printing rule. Check console for details.');
            } finally {
                printBtn.textContent = originalPrintBtnText;
                printBtn.disabled = false;
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadIndexes();
        });

        // Toggle Email Action Fields visibility
        function toggleEmailActionFields() {
            const emailFieldsDiv = document.getElementById('emailActionFields');
            const enableCheckbox = document.getElementById('enableEmailAction');
            if (enableCheckbox.checked) {
                emailFieldsDiv.style.display = 'block';
            } else {
                emailFieldsDiv.style.display = 'none';
            }
        }
    </script>
</body>
</html> 