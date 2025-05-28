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

        .action-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .action-card {
            flex: 1;
            min-width: 200px;
            background: #ffffff;
            border: 2px solid #d3dae6;
            border-radius: 8px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            position: relative;
        }

        .action-card:hover:not(.disabled) {
            border-color: #006bb4;
            box-shadow: 0 2px 8px rgba(0, 107, 180, 0.15);
            transform: translateY(-2px);
        }

        .action-card.selected {
            border-color: #006bb4;
            background: #f0f8ff;
            box-shadow: 0 2px 8px rgba(0, 107, 180, 0.2);
        }

        .action-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f7f9fb;
        }

        .action-card.disabled:hover {
            transform: none;
            box-shadow: none;
            border-color: #d3dae6;
        }

        .action-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            color: #69707d;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-card.selected .action-icon {
            color: #006bb4;
        }

        .action-card.disabled .action-icon {
            color: #98a2b3;
        }

        .action-label {
            font-size: 16px;
            font-weight: 600;
            color: #343741;
            margin-bottom: 8px;
        }

        .action-card.selected .action-label {
            color: #006bb4;
        }

        .action-description {
            font-size: 13px;
            color: #69707d;
            line-height: 1.4;
        }

        .integration-header {
            margin-bottom: 16px;
        }

        .integration-title {
            font-size: 18px;
            font-weight: 600;
            color: #343741;
        }

        .integration-description {
            font-size: 13px;
            color: #69707d;
        }

        .integration-options {
            display: flex;
            flex-direction: column;
        }

        .integration-preview {
            background: #ffffff;
            border: 1px solid #d3dae6;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .integration-preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .integration-badge {
            background: #006bb4;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .integration-details {
            margin-top: 16px;
        }

        .detail-row {
            margin-bottom: 8px;
        }

        .detail-label {
            font-size: 14px;
            font-weight: 600;
            color: #343741;
        }

        .selected-integration {
            background: #f7f9fb;
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

            <!-- Action Selection Grid -->
            <div class="action-grid">
                <div class="action-card" id="emailActionCard" onclick="selectAction('email')">
                    <div class="action-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C2.89,4 2,4.89 2,4Z"/>
                        </svg>
                    </div>
                    <div class="action-label">Email</div>
                    <div class="action-description">Send email notifications</div>
                </div>

                <div class="action-card disabled" id="slackActionCard" onclick="selectAction('slack')">
                    <div class="action-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.527 2.527 0 0 1 2.521 2.521 2.527 2.527 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/>
                        </svg>
                    </div>
                    <div class="action-label">Slack</div>
                    <div class="action-description">Coming soon</div>
                </div>

                <div class="action-card disabled" id="discordActionCard" onclick="selectAction('discord')">
                    <div class="action-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                        </svg>
                    </div>
                    <div class="action-label">Discord</div>
                    <div class="action-description">Coming soon</div>
                </div>
            </div>

            <!-- Email Integration Selection -->
            <div id="emailIntegrationSection" style="display: none; margin-top: 24px;">
                <div class="integration-header">
                    <h3 class="integration-title">Select Email Integration</h3>
                    <div class="integration-description">Choose an existing email integration or enter custom settings.</div>
                </div>

                <div class="integration-options">
                    <div class="form-group">
                        <label class="form-label">Email Integration</label>
                        <div class="form-description">Select a pre-configured email integration or choose "Custom" to enter manual settings.</div>
                        <select id="emailIntegrationSelect" class="form-input" onchange="handleEmailIntegrationChange()">
                            <option value="">Select an integration...</option>
                            <option value="custom">Custom Settings</option>
                        </select>
                    </div>

                    <!-- Selected Integration Display -->
                    <div id="selectedIntegrationDisplay" style="display: none; margin-top: 16px;">
                        <div class="integration-preview">
                            <div class="integration-preview-header">
                                <strong id="integrationName"></strong>
                                <span class="integration-badge">Selected</span>
                            </div>
                            <div class="integration-details">
                                <div class="detail-row">
                                    <span class="detail-label">SMTP Host:</span>
                                    <span id="integrationSmtpHost"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Port:</span>
                                    <span id="integrationSmtpPort"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">From Address:</span>
                                    <span id="integrationFromAddress"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Email Settings -->
                    <div id="customEmailFields" style="display: none; margin-top: 20px; padding-left: 0;">
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

                    <!-- Recipient Email for Integration -->
                    <div id="integrationRecipientField" style="display: none; margin-top: 20px;">
                        <div class="form-group">
                            <label class="form-label" for="integrationEmailRecipient">Recipient Email(s)</label>
                            <div class="form-description">Enter the email addresses that should receive alerts (overrides default recipient if set).</div>
                            <input type="email" id="integrationEmailRecipient" class="form-input" placeholder="e.g., user1@example.com, user2@example.com">
                        </div>
                    </div>
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
            <button type="button" class="btn btn-primary" onclick="reviewDataRuleIntegration()">
                Review Rule
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

        // Global variables
        let selectedAction = null;
        let emailIntegrations = [];

        // Action selection function
        function selectAction(action) {
            console.log(`Selected action: ${action}`);
            
            // Clear previous selections
            document.querySelectorAll('.action-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Hide all action sections
            document.getElementById('emailIntegrationSection').style.display = 'none';
            
            if (action === 'email') {
                // Mark email card as selected
                document.getElementById('emailActionCard').classList.add('selected');
                selectedAction = 'email';
                
                // Show email integration section
                document.getElementById('emailIntegrationSection').style.display = 'block';
                
                // Load email integrations if not already loaded
                if (emailIntegrations.length === 0) {
                    loadEmailIntegrations();
                }
            } else if (action === 'slack' || action === 'discord') {
                // Show coming soon message for disabled actions
                alert(`${action.charAt(0).toUpperCase() + action.slice(1)} integration is coming soon!`);
            } else {
                selectedAction = null;
            }
        }

        // Load email integrations from the database
        async function loadEmailIntegrations() {
            try {
                const response = await fetch('/api/elasticsearch/integrations/email/list');
                const result = await response.json();
                
                if (result.success) {
                    emailIntegrations = result.integrations;
                    populateEmailIntegrationSelect();
                } else {
                    console.error('Failed to load email integrations:', result.error);
                    alert('Failed to load email integrations. You can still use custom settings.');
                }
            } catch (error) {
                console.error('Error loading email integrations:', error);
                alert('Error loading email integrations. You can still use custom settings.');
            }
        }

        // Populate the email integration dropdown
        function populateEmailIntegrationSelect() {
            const select = document.getElementById('emailIntegrationSelect');
            
            // Clear existing options except the first two (placeholder and custom)
            while (select.children.length > 2) {
                select.removeChild(select.lastChild);
            }
            
            // Add integration options
            emailIntegrations.forEach(integration => {
                const option = document.createElement('option');
                option.value = integration.id;
                option.textContent = integration.name;
                option.dataset.integration = JSON.stringify(integration);
                select.appendChild(option);
            });
        }

        // Email integration change handler
        function handleEmailIntegrationChange() {
            const select = document.getElementById('emailIntegrationSelect');
            const selectedValue = select.value;
            
            // Hide all sub-sections
            document.getElementById('selectedIntegrationDisplay').style.display = 'none';
            document.getElementById('customEmailFields').style.display = 'none';
            document.getElementById('integrationRecipientField').style.display = 'none';
            
            if (selectedValue === 'custom') {
                // Show custom email fields
                document.getElementById('customEmailFields').style.display = 'block';
            } else if (selectedValue && selectedValue !== '') {
                // Show selected integration
                const selectedOption = select.options[select.selectedIndex];
                const integration = JSON.parse(selectedOption.dataset.integration);
                
                displaySelectedIntegration(integration);
                document.getElementById('selectedIntegrationDisplay').style.display = 'block';
                document.getElementById('integrationRecipientField').style.display = 'block';
                
                // Pre-fill recipient field with default if available
                const recipientField = document.getElementById('integrationEmailRecipient');
                if (integration.default_recipient) {
                    recipientField.value = integration.default_recipient;
                }
            }
        }

        // Display selected integration details
        function displaySelectedIntegration(integration) {
            document.getElementById('integrationName').textContent = integration.name;
            document.getElementById('integrationSmtpHost').textContent = integration.smtp_host;
            document.getElementById('integrationSmtpPort').textContent = integration.smtp_port;
            document.getElementById('integrationFromAddress').textContent = integration.from_address;
        }

        // Get email configuration for form submission
        function getEmailConfiguration() {
            if (selectedAction !== 'email') {
                return null;
            }
            
            const select = document.getElementById('emailIntegrationSelect');
            const selectedValue = select.value;
            
            if (selectedValue === 'custom') {
                // Return custom configuration
                return {
                    type: 'custom',
                    emailRecipient: document.getElementById('emailRecipient').value.trim(),
                    smtpHost: document.getElementById('smtpHost').value.trim(),
                    smtpPort: document.getElementById('smtpPort').value,
                    smtpSsl: document.getElementById('smtpSsl').checked,
                    fromAddress: document.getElementById('fromAddress').value.trim(),
                    smtpUsername: document.getElementById('smtpUsername').value.trim(),
                    smtpPassword: document.getElementById('smtpPassword').value
                };
            } else if (selectedValue && selectedValue !== '') {
                // Return integration configuration
                const selectedOption = select.options[select.selectedIndex];
                const integration = JSON.parse(selectedOption.dataset.integration);
                const recipient = document.getElementById('integrationEmailRecipient').value.trim();
                
                return {
                    type: 'integration',
                    integrationId: integration.id,
                    integrationName: integration.name,
                    emailRecipient: recipient || integration.default_recipient,
                    smtpHost: integration.smtp_host,
                    smtpPort: integration.smtp_port,
                    smtpSsl: integration.smtp_ssl,
                    fromAddress: integration.from_address,
                    // Note: We don't include username/password from integration for security
                };
            }
            
            return null;
        }

        // Update the existing form validation to work with new structure
        function validateEmailConfiguration() {
            if (selectedAction !== 'email') {
                return true; // No email action selected, so no validation needed
            }
            
            const emailConfig = getEmailConfiguration();
            if (!emailConfig) {
                alert('Please select an email integration or configure custom settings.');
                return false;
            }
            
            if (!emailConfig.emailRecipient) {
                alert('Please specify recipient email addresses.');
                return false;
            }
            
            if (emailConfig.type === 'custom') {
                if (!emailConfig.smtpHost || !emailConfig.smtpPort || !emailConfig.fromAddress) {
                    alert('Please fill in all required email configuration fields.');
                    return false;
                }
            }
            
            return true;
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

            // Validate email configuration if email action is selected
            if (!validateEmailConfiguration()) {
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
                unit: document.getElementById('scheduleUnit').value,
                selectedAction: selectedAction
            };

            // Add email configuration if email action is selected
            const emailConfig = getEmailConfiguration();
            if (emailConfig) {
                formData.enableEmailAction = true;
                formData.emailType = emailConfig.type;
                formData.emailRecipient = emailConfig.emailRecipient;
                formData.smtpHost = emailConfig.smtpHost;
                formData.smtpPort = emailConfig.smtpPort;
                formData.smtpSsl = emailConfig.smtpSsl;
                formData.fromAddress = emailConfig.fromAddress;
                
                if (emailConfig.type === 'custom') {
                    formData.smtpUsername = emailConfig.smtpUsername;
                    formData.smtpPassword = emailConfig.smtpPassword;
                } else if (emailConfig.type === 'integration') {
                    formData.integrationId = emailConfig.integrationId;
                    formData.integrationName = emailConfig.integrationName;
                }
            } else {
                formData.enableEmailAction = false;
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

        // Review rule function
        async function reviewDataRuleIntegration() {
            console.log('Review rule button clicked');
            
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
                unit: document.getElementById('scheduleUnit').value,
                selectedAction: selectedAction
            };

            // Add email configuration if email action is selected
            const emailConfig = getEmailConfiguration();
            if (emailConfig) {
                formData.enableEmailAction = true;
                formData.emailType = emailConfig.type;
                formData.emailRecipient = emailConfig.emailRecipient;
                formData.smtpHost = emailConfig.smtpHost;
                formData.smtpPort = emailConfig.smtpPort;
                formData.smtpSsl = emailConfig.smtpSsl;
                formData.fromAddress = emailConfig.fromAddress;
                
                if (emailConfig.type === 'custom') {
                    formData.smtpUsername = emailConfig.smtpUsername;
                    formData.smtpPassword = emailConfig.smtpPassword;
                } else if (emailConfig.type === 'integration') {
                    formData.integrationId = emailConfig.integrationId;
                    formData.integrationName = emailConfig.integrationName;
                }
            } else {
                formData.enableEmailAction = false;
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
    </script>
</body>
</html> 