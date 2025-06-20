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

        /* Specific styling for select dropdowns */
        .form-select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            background: #ffffff;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .form-select:focus {
            outline: none;
            border-color: #006bb4;
            box-shadow: 0 0 0 3px rgba(0, 107, 180, 0.1);
            background-color: #f8f9fa;
        }

        .form-select:hover {
            border-color: #98a2b3;
            background-color: #f8f9fa;
        }

        .form-select option {
            padding: 8px 12px;
            font-size: 14px;
            background: #ffffff;
            color: #343741;
        }

        .form-select option:checked {
            background: #006bb4;
            color: #ffffff;
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

        /* Specific button styles */
        .btn-generate {
            background: #28a745;
            border-color: #28a745;
            font-weight: 600;
        }

        .btn-generate:hover {
            background: #218838;
            border-color: #1e7e34;
        }

        .btn-review {
            background: #17a2b8;
            border-color: #17a2b8;
            font-weight: 500;
        }

        .btn-review:hover {
            background: #138496;
            border-color: #117a8b;
        }

        /* Smooth transitions for email integration sections */
        #selectedIntegrationDisplay,
        #customEmailFields,
        #integrationRecipientField {
            transition: all 0.3s ease;
            opacity: 1;
        }

        #selectedIntegrationDisplay[style*="display: none"],
        #customEmailFields[style*="display: none"],
        #integrationRecipientField[style*="display: none"] {
            opacity: 0;
        }

        /* Integration preview styling */
        .integration-preview {
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .integration-preview:hover {
            border-color: #006bb4;
            box-shadow: 0 2px 8px rgba(0, 107, 180, 0.1);
        }

        .integration-badge {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
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

        <!-- Error and Success Messages -->
        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <div style="color: #dc2626; font-weight: 600; margin-bottom: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="display: inline; margin-right: 8px;">
                        <path d="M12,2L13.09,8.26L22,9L13.09,9.74L12,16L10.91,9.74L2,9L10.91,8.26L12,2Z"/>
                    </svg>
                    Error
                </div>
                @foreach ($errors->all() as $error)
                    <div style="color: #dc2626; font-size: 14px; margin-bottom: 4px;">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <div style="color: #16a34a; font-weight: 600;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="display: inline; margin-right: 8px;">
                        <path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <div class="form-section">
            <h2 class="section-title">Rule Configuration</h2>
            
            <form id="alertForm" method="POST" action="{{ route('elasticsearch.generate-rule') }}">
                @csrf
                <!-- Rule Name Field -->
                <div class="form-group">
                    <label class="form-label" for="ruleName">Rule Name</label>
                    <div class="form-description">
                        Give your alert rule a descriptive name that helps identify its purpose.
                    </div>
                    <input 
                        type="text" 
                        id="ruleName" 
                        name="ruleName"
                        class="form-input" 
                        placeholder="e.g., Payment Service Error Alert, High Response Time Monitor..."
                        value="{{ old('ruleName') }}"
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
                        name="index"
                        class="form-select" 
                        required>
                        <option value="">Select an index...</option>
                        @php
                            // Load indexes server-side
                            try {
                                $indexController = app(\App\Http\Controllers\IndexController::class);
                                $response = $indexController->getIndexes();
                                $indexes = json_decode($response->getContent(), true);
                                if (is_array($indexes)) {
                                    foreach ($indexes as $index) {
                                        echo "<option value=\"{$index['display_name']}\" data-backing-index=\"{$index['name']}\">{$index['display_name']} ({$index['docs_count']} docs)</option>";
                                    }
                                }
                            } catch (Exception $e) {
                                echo '<option value="">Unable to load indexes</option>';
                            }
                        @endphp
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
                        name="prompt"
                        class="form-input form-textarea prompt-textarea" 
                        placeholder="Example: Alert me when there are more than 10 error logs in the last 5 minutes from the payment service, or when the response time exceeds 2 seconds..."
                        required>{{ old('prompt') }}</textarea>
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
                        name="kql"
                        class="form-input form-textarea kql-textarea" 
                        placeholder="level: error AND service.name: payment"
                        >{{ old('kql') }}</textarea>
                    <div class="helper-text">
                        Examples: <code>status: 500</code>, <code>message: "timeout" AND host.name: web-*</code>, <code>response_time > 2000</code>
                    </div>
                </div>
            
                <!-- Rule Schedule Section -->
                <div class="form-section schedule-section">
                    <h2 class="section-title">Rule Schedule</h2>
                    <div class="form-description" style="margin-bottom: 20px;">
                        Set the frequency to check the alert conditions
                    </div>
                    
                    <div class="schedule-row">
                        <span>Check every</span>
                        <input type="number" value="{{ old('interval', '5') }}" name="interval" class="schedule-input" min="1" max="1440" id="scheduleInterval">
                        <select class="form-select" name="unit" id="scheduleUnit" style="min-width: 120px;">
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

                        <div class="action-card" id="slackActionCard" onclick="selectAction('slack')">
                            <div class="action-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.527 2.527 0 0 1 2.521 2.521 2.527 2.527 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/>
                                </svg>
                            </div>
                            <div class="action-label">Slack</div>
                            <div class="action-description">Send Slack notifications</div>
                        </div>

                        <div class="action-card disabled" id="discordActionCard">
                            <div class="action-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                                </svg>
                            </div>
                            <div class="action-label">Discord</div>
                            <div class="action-description">Coming soon</div>
                        </div>
                    </div>

                    <!-- Hidden field to track selected action -->
                    <input type="hidden" name="selectedAction" id="selectedAction" value="">
                    <input type="hidden" name="enableEmailAction" id="enableEmailAction" value="false">
                    <input type="hidden" name="enableSlackAction" id="enableSlackAction" value="false">

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
                                <select id="emailIntegrationSelect" name="emailIntegration" class="form-select" onchange="handleEmailIntegrationChange()">
                                    <option value="">Select an integration...</option>
                                    <option value="custom">Custom Settings</option>
                                    @php
                                        // Load email integrations server-side
                                        try {
                                            $integrationsController = app(\App\Http\Controllers\IntegrationsController::class);
                                            $response = $integrationsController->getEmailIntegrations();
                                            $result = json_decode($response->getContent(), true);
                                            if ($result['success'] && is_array($result['integrations'])) {
                                                foreach ($result['integrations'] as $integration) {
                                                    echo "<option value=\"{$integration['id']}\" data-integration='" . htmlspecialchars(json_encode($integration)) . "'>{$integration['name']}</option>";
                                                }
                                            }
                                        } catch (Exception $e) {
                                            // Silent fail, custom option is still available
                                        }
                                    @endphp
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

                            <!-- Hidden fields for integration data -->
                            <input type="hidden" name="emailType" id="emailType" value="">
                            <input type="hidden" name="integrationId" id="integrationId" value="">
                            <input type="hidden" name="integrationName" id="integrationName" value="">

                            <!-- Custom Email Settings -->
                            <div id="customEmailFields" style="display: none; margin-top: 20px; padding-left: 0;">
                                <div class="form-group">
                                    <label class="form-label" for="emailRecipient">Recipient Email(s)</label>
                                    <div class="form-description">Enter one or more email addresses, separated by commas.</div>
                                    <input type="email" id="emailRecipient" name="emailRecipient" class="form-input" placeholder="e.g., user1@example.com, user2@example.com">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="smtpHost">SMTP Host</label>
                                    <input type="text" id="smtpHost" name="smtpHost" class="form-input" placeholder="e.g., smtp.gmail.com">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="smtpPort">SMTP Port</label>
                                    <input type="number" id="smtpPort" name="smtpPort" class="form-input" placeholder="e.g., 465 or 587">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" style="display: flex; align-items: center;">
                                        <input type="checkbox" id="smtpSsl" name="smtpSsl" style="margin-right: 10px; width: 16px; height: 16px;" checked>
                                        Use SSL for SMTP
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="fromAddress">From Address</label>
                                    <input type="email" id="fromAddress" name="fromAddress" class="form-input" placeholder="e.g., alerts@yourdomain.com">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="smtpUsername">SMTP Username (Email)</label>
                                    <input type="email" id="smtpUsername" name="smtpUsername" class="form-input" placeholder="Usually your email address">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="smtpPassword">SMTP Password</label>
                                    <input type="password" id="smtpPassword" name="smtpPassword" class="form-input">
                                </div>
                            </div>

                            <!-- Recipient Email for Integration -->
                            <div id="integrationRecipientField" style="display: none; margin-top: 20px;">
                                <div class="form-group">
                                    <label class="form-label" for="integrationEmailRecipient">Recipient Email(s)</label>
                                    <div class="form-description">Enter the email addresses that should receive alerts (overrides default recipient if set).</div>
                                    <input type="email" id="integrationEmailRecipient" name="integrationEmailRecipient" class="form-input" placeholder="e.g., user1@example.com, user2@example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slack Integration Selection -->
                    <div id="slackIntegrationSection" style="display: none; margin-top: 24px;">
                        <div class="integration-header">
                            <h3 class="integration-title">Select Slack Integration</h3>
                            <div class="integration-description">Choose an existing Slack integration or enter custom settings.</div>
                        </div>

                        <div class="integration-options">
                            <div class="form-group">
                                <label class="form-label">Slack Integration</label>
                                <div class="form-description">Select a pre-configured Slack integration or choose "Custom" to enter manual settings.</div>
                                <select id="slackIntegrationSelect" name="slackIntegration" class="form-select" onchange="handleSlackIntegrationChange()">
                                    <option value="">Select an integration...</option>
                                    <option value="custom">Custom Settings</option>
                                    @php
                                        // Load Slack integrations server-side
                                        try {
                                            $integrationsController = app(\App\Http\Controllers\IntegrationsController::class);
                                            $response = $integrationsController->getSlackIntegrations();
                                            $result = json_decode($response->getContent(), true);
                                            if ($result['success'] && is_array($result['integrations'])) {
                                                foreach ($result['integrations'] as $integration) {
                                                    echo "<option value=\"{$integration['id']}\" data-integration='" . htmlspecialchars(json_encode($integration)) . "'>{$integration['name']}</option>";
                                                }
                                            }
                                        } catch (Exception $e) {
                                            // Silent fail, custom option is still available
                                        }
                                    @endphp
                                </select>
                            </div>

                            <!-- Selected Slack Integration Display -->
                            <div id="selectedSlackIntegrationDisplay" style="display: none; margin-top: 16px;">
                                <div class="integration-preview">
                                    <div class="integration-preview-header">
                                        <strong id="slackIntegrationName"></strong>
                                        <span class="integration-badge">Selected</span>
                                    </div>
                                    <div class="integration-details">
                                        <div class="detail-row">
                                            <span class="detail-label">Channel:</span>
                                            <span id="slackIntegrationChannel"></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Username:</span>
                                            <span id="slackIntegrationUsername"></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Icon:</span>
                                            <span id="slackIntegrationIcon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for Slack integration data -->
                            <input type="hidden" name="slackType" id="slackType" value="">
                            <input type="hidden" name="slackIntegrationId" id="slackIntegrationId" value="">
                            <input type="hidden" name="slackIntegrationName" id="slackIntegrationName" value="">

                            <!-- Custom Slack Settings -->
                            <div id="customSlackFields" style="display: none; margin-top: 20px; padding-left: 0;">
                                <div class="form-group">
                                    <label class="form-label" for="slackWebhookUrl">Slack Webhook URL</label>
                                    <div class="form-description">Enter your Slack webhook URL (https://hooks.slack.com/services/...).</div>
                                    <input type="url" id="slackWebhookUrl" name="slackWebhookUrl" class="form-input" placeholder="https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="slackChannel">Channel</label>
                                    <div class="form-description">Channel to post alerts to (e.g., #alerts).</div>
                                    <input type="text" id="slackChannel" name="slackChannel" class="form-input" placeholder="#alerts">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="slackUsername">Bot Username</label>
                                    <div class="form-description">Username for the bot posting the message.</div>
                                    <input type="text" id="slackUsername" name="slackUsername" class="form-input" placeholder="ElastAlert">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="slackIconEmoji">Icon Emoji</label>
                                    <div class="form-description">Emoji to use as the bot icon (e.g., :warning:).</div>
                                    <input type="text" id="slackIconEmoji" name="slackIconEmoji" class="form-input" placeholder=":warning:">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('elasticsearch.index') }}" style="text-decoration: none;" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" id="generateRuleBtn" class="btn btn-primary btn-generate">
                        Generate ElastAlert Rule
                    </button>
                    <button type="submit" id="reviewConfigBtn" formaction="{{ route('elasticsearch.print-rule.post') }}" formmethod="POST" class="btn btn-primary btn-review">
                        Review Configuration
                    </button>
                </div>
            </form>
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
            autoResize(textarea);
        });

        // Action selection function
        function selectAction(action) {
            document.querySelectorAll('.action-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            document.getElementById('emailIntegrationSection').style.display = 'none';
            document.getElementById('slackIntegrationSection').style.display = 'none';
            
            // Reset all action states
            document.getElementById('selectedAction').value = '';
            document.getElementById('enableEmailAction').value = 'false';
            document.getElementById('enableSlackAction').value = 'false';
            
            if (action === 'email') {
                document.getElementById('emailActionCard').classList.add('selected');
                document.getElementById('selectedAction').value = 'email';
                document.getElementById('enableEmailAction').value = 'true';
                document.getElementById('emailIntegrationSection').style.display = 'block';
            } else if (action === 'slack') {
                document.getElementById('slackActionCard').classList.add('selected');
                document.getElementById('selectedAction').value = 'slack';
                document.getElementById('enableSlackAction').value = 'true';
                document.getElementById('slackIntegrationSection').style.display = 'block';
            }
        }

        // Email integration change handler
        function handleEmailIntegrationChange() {
            const select = document.getElementById('emailIntegrationSelect');
            if (!select) return;
            
            const selectedValue = select.value;
            
            // Get all elements that need to be controlled
            const integrationDisplay = document.getElementById('selectedIntegrationDisplay');
            const customFields = document.getElementById('customEmailFields');
            const recipientField = document.getElementById('integrationRecipientField');
            const emailType = document.getElementById('emailType');
            const integrationId = document.getElementById('integrationId');
            const integrationName = document.getElementById('integrationName');
            
            // Hide all sections first
            if (integrationDisplay) integrationDisplay.style.display = 'none';
            if (customFields) customFields.style.display = 'none';
            if (recipientField) recipientField.style.display = 'none';
            
            // Clear hidden field values
            if (emailType) emailType.value = '';
            if (integrationId) integrationId.value = '';
            if (integrationName) integrationName.value = '';
            
            if (selectedValue === 'custom') {
                // Show custom email fields
                if (customFields) customFields.style.display = 'block';
                if (emailType) emailType.value = 'custom';
            } else if (selectedValue && selectedValue !== '') {
                // Handle integration selection
                try {
                    const selectedOption = select.options[select.selectedIndex];
                    const integrationData = selectedOption.getAttribute('data-integration');
                    
                    if (integrationData) {
                        const integration = JSON.parse(integrationData);
                        
                        // Display integration details
                        displaySelectedIntegration(integration);
                        if (integrationDisplay) integrationDisplay.style.display = 'block';
                        if (recipientField) recipientField.style.display = 'block';
                        
                        // Set hidden field values
                        if (emailType) emailType.value = 'integration';
                        if (integrationId) integrationId.value = integration.id;
                        if (integrationName) integrationName.value = integration.name;
                        
                        // Pre-fill recipient field
                        const emailRecipientField = document.getElementById('integrationEmailRecipient');
                        if (emailRecipientField && integration.default_recipient) {
                            emailRecipientField.value = integration.default_recipient;
                        }
                    }
                } catch (error) {
                    console.error('Error parsing integration data:', error);
                    // Fallback to custom settings if parsing fails
                    if (customFields) customFields.style.display = 'block';
                    if (emailType) emailType.value = 'custom';
                }
            }
        }

        // Display selected integration details
        function displaySelectedIntegration(integration) {
            if (!integration) return;
            
            const nameElement = document.getElementById('integrationName');
            const hostElement = document.getElementById('integrationSmtpHost');
            const portElement = document.getElementById('integrationSmtpPort');
            const fromElement = document.getElementById('integrationFromAddress');
            
            if (nameElement) nameElement.textContent = integration.name || 'Unknown';
            if (hostElement) hostElement.textContent = integration.smtp_host || 'Not specified';
            if (portElement) portElement.textContent = integration.smtp_port || 'Not specified';
            if (fromElement) fromElement.textContent = integration.from_address || 'Not specified';
        }

        // Slack integration change handler
        function handleSlackIntegrationChange() {
            const select = document.getElementById('slackIntegrationSelect');
            if (!select) return;
            
            const selectedValue = select.value;
            
            // Get all elements that need to be controlled
            const integrationDisplay = document.getElementById('selectedSlackIntegrationDisplay');
            const customFields = document.getElementById('customSlackFields');
            const slackType = document.getElementById('slackType');
            const slackIntegrationId = document.getElementById('slackIntegrationId');
            const slackIntegrationName = document.getElementById('slackIntegrationName');
            
            // Hide all sections first
            if (integrationDisplay) integrationDisplay.style.display = 'none';
            if (customFields) customFields.style.display = 'none';
            
            // Clear hidden field values
            if (slackType) slackType.value = '';
            if (slackIntegrationId) slackIntegrationId.value = '';
            if (slackIntegrationName) slackIntegrationName.value = '';
            
            if (selectedValue === 'custom') {
                // Show custom Slack fields
                if (customFields) customFields.style.display = 'block';
                if (slackType) slackType.value = 'custom';
            } else if (selectedValue && selectedValue !== '') {
                // Handle integration selection
                try {
                    const selectedOption = select.options[select.selectedIndex];
                    const integrationData = selectedOption.getAttribute('data-integration');
                    
                    if (integrationData) {
                        const integration = JSON.parse(integrationData);
                        
                        // Display integration details
                        displaySelectedSlackIntegration(integration);
                        if (integrationDisplay) integrationDisplay.style.display = 'block';
                        
                        // Set hidden field values
                        if (slackType) slackType.value = 'integration';
                        if (slackIntegrationId) slackIntegrationId.value = integration.id;
                        if (slackIntegrationName) slackIntegrationName.value = integration.name;
                    }
                } catch (error) {
                    console.error('Error parsing Slack integration data:', error);
                    // Fallback to custom settings if parsing fails
                    if (customFields) customFields.style.display = 'block';
                    if (slackType) slackType.value = 'custom';
                }
            }
        }

        // Display selected Slack integration details
        function displaySelectedSlackIntegration(integration) {
            if (!integration) return;
            
            const nameElement = document.getElementById('slackIntegrationName');
            const channelElement = document.getElementById('slackIntegrationChannel');
            const usernameElement = document.getElementById('slackIntegrationUsername');
            const iconElement = document.getElementById('slackIntegrationIcon');
            
            if (nameElement) nameElement.textContent = integration.name || 'Unknown';
            if (channelElement) channelElement.textContent = integration.channel || 'Not specified';
            if (usernameElement) usernameElement.textContent = integration.username || 'Not specified';
            if (iconElement) iconElement.textContent = integration.icon_emoji || 'Not specified';
        }
    </script>
</body>
</html> 