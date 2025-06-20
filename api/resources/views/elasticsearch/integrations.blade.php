<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrations - Elasticsearch</title>
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

        .icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .icon-discover { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z'/%3E%3C/svg%3E"); 
        }

        .icon-calendar { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z'/%3E%3C/svg%3E"); 
        }

        .icon-refresh { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z'/%3E%3C/svg%3E"); 
        }

        .icon-add { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z'/%3E%3C/svg%3E"); 
        }

        .icon-rules { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M3 13H11V11H3M3 6V8H21V6M3 18V16H21V18Z'/%3E%3C/svg%3E");
        }

        .icon-integrations { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M12,2A2,2 0 0,1 14,4C14,4.74 13.6,5.39 13,5.73V7H14A7,7 0 0,1 21,14H22A1,1 0 0,1 23,15V18A1,1 0 0,1 22,19H21V20A2,2 0 0,1 19,22H5A2,2 0 0,1 3,20V19H2A1,1 0 0,1 1,18V15A1,1 0 0,1 2,14H3A7,7 0 0,1 10,7H11V5.73C10.4,5.39 10,4.74 10,4A2,2 0 0,1 12,2M7.5,13A2.5,2.5 0 0,0 5,15.5A2.5,2.5 0 0,0 7.5,18A2.5,2.5 0 0,0 10,15.5A2.5,2.5 0 0,0 7.5,13M16.5,13A2.5,2.5 0 0,0 14,15.5A2.5,2.5 0 0,0 16.5,18A2.5,2.5 0 0,0 19,15.5A2.5,2.5 0 0,0 16.5,13Z'/%3E%3C/svg%3E");
        }

        .icon-email {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z'/%3E%3C/svg%3E");
        }

        .icon-slack {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M6.527,14.514c0,1.324-1.074,2.398-2.398,2.398s-2.398-1.074-2.398-2.398s1.074-2.398,2.398-2.398h2.398V14.514z M7.727,14.514c0-1.324,1.074-2.398,2.398-2.398s2.398,1.074,2.398,2.398v6.028c0,1.324-1.074,2.398-2.398,2.398s-2.398-1.074-2.398-2.398V14.514z M10.125,6.527c-1.324,0-2.398-1.074-2.398-2.398s1.074-2.398,2.398-2.398s2.398,1.074,2.398,2.398v2.398H10.125z M10.125,7.727c1.324,0,2.398,1.074,2.398,2.398s-1.074,2.398-2.398,2.398H4.097c-1.324,0-2.398-1.074-2.398-2.398s1.074-2.398,2.398-2.398H10.125z M17.473,10.125c0-1.324,1.074-2.398,2.398-2.398s2.398,1.074,2.398,2.398s-1.074,2.398-2.398,2.398h-2.398V10.125z M16.273,10.125c0,1.324-1.074,2.398-2.398,2.398s-2.398-1.074-2.398-2.398V4.097c0-1.324,1.074-2.398,2.398-2.398s2.398,1.074,2.398,2.398V10.125z M13.875,17.473c1.324,0,2.398,1.074,2.398,2.398s-1.074,2.398-2.398,2.398s-2.398-1.074-2.398-2.398v-2.398H13.875z M13.875,16.273c-1.324,0-2.398-1.074-2.398-2.398s1.074-2.398,2.398-2.398h6.028c1.324,0,2.398,1.074,2.398,2.398s-1.074,2.398-2.398,2.398H13.875z'/%3E%3C/svg%3E");
        }

        .icon-edit {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z'/%3E%3C/svg%3E");
        }

        .icon-delete {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23bd271e'%3E%3Cpath d='M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z'/%3E%3C/svg%3E");
        }

        .icon-test {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2300875a'%3E%3Cpath d='M12,2C13.1,2 14,2.9 14,4C14,5.1 13.1,6 12,6C10.9,6 10,5.1 10,4C10,2.9 10.9,2 12,2M21,9V7L15,1L9,7V9H21M12,10A5,5 0 0,0 7,15A5,5 0 0,0 12,20A5,5 0 0,0 17,15A5,5 0 0,0 12,10Z'/%3E%3C/svg%3E");
        }

        .integrations-header {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .integrations-title {
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

        .main-container {
            padding: 0;
            min-height: calc(100vh);
        }

        .integrations-container {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
        }

        .integrations-sidebar {
            width: 280px;
            background: #ffffff;
            border-right: 1px solid #d3dae6;
            padding: 20px;
            overflow-y: auto;
        }

        .integrations-sidebar h3 {
            font-size: 16px;
            font-weight: 600;
            color: #343741;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .integrations-types {
            list-style: none;
        }

        .integration-type {
            padding: 12px 16px;
            margin-bottom: 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .integration-type:hover {
            background: #f7f9fb;
        }

        .integration-type.active {
            background: #e7f3ff;
            color: #006bb4;
            border-left: 3px solid #006bb4;
        }

        .integrations-content {
            flex-grow: 1;
            padding: 24px;
            background-color: #ffffff;
            overflow-y: auto;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f2f5;
        }

        .content-title {
            font-size: 20px;
            font-weight: 600;
            color: #343741;
        }

        .integrations-list {
            margin-bottom: 24px;
        }

        .integration-card {
            border: 1px solid #d3dae6;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .integration-card:hover {
            border-color: #006bb4;
            box-shadow: 0 2px 4px rgba(0, 107, 180, 0.1);
        }

        .integration-card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 8px;
        }

        .integration-name {
            font-weight: 600;
            color: #343741;
            font-size: 16px;
            flex: 1;
        }

        .integration-actions {
            display: flex;
            gap: 8px;
        }

        .integration-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .integration-btn.edit {
            background: #e7f3ff;
            color: #006bb4;
        }

        .integration-btn.edit:hover {
            background: #d1ebff;
        }

        .integration-btn.delete {
            background: #fef2f2;
            color: #bd271e;
        }

        .integration-btn.delete:hover {
            background: #fee2e2;
        }

        .integration-btn.test {
            background: #e6f7e6;
            color: #00875a;
        }

        .integration-btn.test:hover {
            background: #d4f2d4;
        }

        .integration-details {
            color: #69707d;
            font-size: 14px;
            line-height: 1.4;
        }

        .integration-form {
            display: none;
            background: #f7f9fb;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            padding: 20px;
            margin-top: 16px;
        }

        .integration-form.show {
            display: block;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: #343741;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            font-size: 14px;
            color: #343741;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #006bb4;
            box-shadow: 0 0 0 2px rgba(0, 107, 180, 0.1);
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .form-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn.primary {
            background: #006bb4;
            color: #ffffff;
        }

        .btn.primary:hover {
            background: #0056a0;
        }

        .btn.secondary {
            background: #ffffff;
            color: #343741;
            border: 1px solid #d3dae6;
        }

        .btn.secondary:hover {
            background: #f7f9fb;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #69707d;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: #343741;
            margin-bottom: 8px;
        }

        .hidden {
            display: none !important;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert.success {
            background: #e6f7e6;
            color: #00875a;
            border: 1px solid #b3e6b3;
        }

        .alert.error {
            background: #fef2f2;
            color: #bd271e;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="integrations-header">
        <a href="{{ route('elasticsearch.index') }}" class="back-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/></svg>
            Back
        </a>
        <h1 class="integrations-title">Integrations</h1>
    </div>
    <div class="integrations-container">
            <!-- Sidebar -->
            <div class="integrations-sidebar">
                <h3>
                    <span class="icon icon-integrations"></span>
                    Integration Types
                </h3>
                <ul class="integrations-types">
                    <li class="integration-type active" data-type="email" onclick="showIntegrationType('email')">
                        <span class="icon icon-email"></span>
                        Email / SMTP
                    </li>
                    <li class="integration-type" data-type="slack" onclick="showIntegrationType('slack')">
                        <span class="icon icon-slack"></span>
                        Slack
                    </li>
                </ul>
            </div>

            <!-- Content Area -->
            <div class="integrations-content">
                @php
                    $emailIntegrations = collect();
                    $slackIntegrations = collect();
                    
                    try {
                        if (class_exists('\App\Models\EmailIntegration')) {
                            $emailIntegrations = \App\Models\EmailIntegration::all();
                        }
                        if (class_exists('\App\Models\SlackIntegration')) {
                            $slackIntegrations = \App\Models\SlackIntegration::all();
                        }
                    } catch (\Exception $e) {
                        // Keep empty collections on error
                        \Log::error('Integration loading error: ' . $e->getMessage());
                    }
                @endphp

                <div class="content-header">
                    <h2 class="content-title" id="pageTitle">Email / SMTP Integrations</h2>
                    <button class="back-button" onclick="showAddForm()">
                        <span class="icon icon-add"></span>
                        Add Integration
                    </button>
                </div>

                <!-- Alert Messages -->
                <div id="alertContainer">
                    @if(session('success'))
                        <div class="alert success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert error">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert error">
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Email Integration Section -->
                <div id="emailSection" class="integration-section">
                    <!-- Email Integration Form -->
                    <div class="integration-form" id="emailIntegrationForm">
                        <form action="{{ route('api.elasticsearch.integrations.email.save') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label" for="emailIntegrationName">Integration Name *</label>
                                <input type="text" class="form-input" id="emailIntegrationName" name="name" placeholder="e.g., Company SMTP Server" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="smtpHost">SMTP Host *</label>
                                <input type="text" class="form-input" id="smtpHost" name="smtp_host" placeholder="e.g., smtp.gmail.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="smtpPort">SMTP Port *</label>
                                <input type="number" class="form-input" id="smtpPort" name="smtp_port" placeholder="e.g., 587" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="smtpUsername">SMTP Username *</label>
                                <input type="text" class="form-input" id="smtpUsername" name="smtp_username" placeholder="e.g., your-email@company.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="smtpPassword">SMTP Password *</label>
                                <input type="password" class="form-input" id="smtpPassword" name="smtp_password" placeholder="Your SMTP password" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="fromAddress">From Address *</label>
                                <input type="email" class="form-input" id="fromAddress" name="from_address" placeholder="e.g., alerts@company.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="defaultRecipient">Default Recipient</label>
                                <input type="email" class="form-input" id="defaultRecipient" name="default_recipient" placeholder="e.g., admin@company.com">
                            </div>
                            
                            <div class="form-checkbox">
                                <input type="checkbox" id="smtpSsl" name="smtp_ssl" checked>
                                <label for="smtpSsl">Use SSL/TLS</label>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn primary">Save Integration</button>
                                <button type="button" class="btn secondary" onclick="hideAddForm()">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Email Integrations List -->
                    <div class="integrations-list" id="emailIntegrationsList">
                        @if($emailIntegrations->count() > 0)
                            @foreach($emailIntegrations as $integration)
                                <div class="integration-card">
                                    <div class="integration-card-header">
                                        <div class="integration-name">{{ $integration->name }}</div>
                                        <div class="integration-actions">
                                            <form action="{{ route('api.elasticsearch.integrations.email.test') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="name" value="{{ $integration->name }}">
                                                <button type="submit" class="integration-btn test">
                                                    <span class="icon icon-test"></span>
                                                    Test
                                                </button>
                                            </form>
                                            <form action="{{ route('api.elasticsearch.integrations.email.delete') }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this integration?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="name" value="{{ $integration->name }}">
                                                <button type="submit" class="integration-btn delete">
                                                    <span class="icon icon-delete"></span>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="integration-details">
                                        <strong>SMTP Host:</strong> {{ $integration->smtp_host }}<br>
                                        <strong>Port:</strong> {{ $integration->smtp_port }}<br>
                                        <strong>From:</strong> {{ $integration->from_address }}<br>
                                        <strong>SSL:</strong> {{ $integration->smtp_ssl ? 'Yes' : 'No' }}<br>
                                        @if($integration->default_recipient)
                                            <strong>Default Recipient:</strong> {{ $integration->default_recipient }}<br>
                                        @endif
                                        <strong>Created:</strong> {{ $integration->created_at->format('Y-m-d H:i:s') }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <h3>No email integrations configured</h3>
                                <p>Add your first email integration to start sending alert notifications.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Slack Integration Section -->
                <div id="slackSection" class="integration-section" style="display: none;">
                    <!-- Slack Integration Form -->
                    <div class="integration-form" id="slackIntegrationForm">
                        <form action="{{ route('api.elasticsearch.integrations.slack.save') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label" for="slackIntegrationName">Integration Name *</label>
                                <input type="text" class="form-input" id="slackIntegrationName" name="name" placeholder="e.g., Company Slack" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="webhookUrl">Webhook URL *</label>
                                <input type="url" class="form-input" id="webhookUrl" name="webhook_url" placeholder="https://hooks.slack.com/services/..." required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="channel">Channel</label>
                                <input type="text" class="form-input" id="channel" name="channel" placeholder="e.g., #alerts">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="username">Bot Username</label>
                                <input type="text" class="form-input" id="username" name="username" placeholder="e.g., ElastAlert Bot">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="iconEmoji">Icon Emoji</label>
                                <input type="text" class="form-input" id="iconEmoji" name="icon_emoji" placeholder="e.g., :warning:">
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn primary">Save Integration</button>
                                <button type="button" class="btn secondary" onclick="hideAddForm()">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Slack Integrations List -->
                    <div class="integrations-list" id="slackIntegrationsList">
                        @if($slackIntegrations->count() > 0)
                            @foreach($slackIntegrations as $integration)
                                <div class="integration-card">
                                    <div class="integration-card-header">
                                        <div class="integration-name">{{ $integration->name }}</div>
                                        <div class="integration-actions">
                                            <form action="{{ route('api.elasticsearch.integrations.slack.test') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="name" value="{{ $integration->name }}">
                                                <button type="submit" class="integration-btn test">
                                                    <span class="icon icon-test"></span>
                                                    Test
                                                </button>
                                            </form>
                                            <form action="{{ route('api.elasticsearch.integrations.slack.delete') }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this integration?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="name" value="{{ $integration->name }}">
                                                <button type="submit" class="integration-btn delete">
                                                    <span class="icon icon-delete"></span>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="integration-details">
                                        <strong>Webhook URL:</strong> {{ substr($integration->webhook_url, 0, 50) }}...<br>
                                        @if($integration->channel)
                                            <strong>Channel:</strong> {{ $integration->channel }}<br>
                                        @endif
                                        @if($integration->username)
                                            <strong>Username:</strong> {{ $integration->username }}<br>
                                        @endif
                                        @if($integration->icon_emoji)
                                            <strong>Icon:</strong> {{ $integration->icon_emoji }}<br>
                                        @endif
                                        <strong>Created:</strong> {{ $integration->created_at->format('Y-m-d H:i:s') }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <h3>No Slack integrations configured</h3>
                                <p>Add your first Slack integration to start sending alert notifications.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <script>
        let currentIntegrationType = 'email';

        // Show specific integration type
        function showIntegrationType(type) {
            // Hide all sections
            document.getElementById('emailSection').style.display = 'none';
            document.getElementById('slackSection').style.display = 'none';
            
            // Hide any open forms
            hideAddForm();
            
            // Update sidebar active state
            document.querySelectorAll('.integration-type').forEach(function(item) {
                item.classList.remove('active');
            });
            document.querySelector(`[data-type="${type}"]`).classList.add('active');
            
            // Show selected section
            if (type === 'slack') {
                document.getElementById('slackSection').style.display = 'block';
                document.getElementById('pageTitle').textContent = 'Slack Integrations';
                currentIntegrationType = 'slack';
            } else {
                document.getElementById('emailSection').style.display = 'block';
                document.getElementById('pageTitle').textContent = 'Email / SMTP Integrations';
                currentIntegrationType = 'email';
            }
        }

        // Show add form for current integration type
        function showAddForm() {
            let formId = currentIntegrationType === 'slack' ? 'slackIntegrationForm' : 'emailIntegrationForm';
            const integrationForm = document.getElementById(formId);
            
            if (integrationForm) {
                integrationForm.classList.add('show');
                
                // Reset the form
                const form = integrationForm.querySelector('form');
                if (form) {
                    form.reset();
                }
            }
        }

        // Hide add form for current integration type
        function hideAddForm() {
            // Hide both forms
            const emailForm = document.getElementById('emailIntegrationForm');
            const slackForm = document.getElementById('slackIntegrationForm');
            
            if (emailForm) {
                emailForm.classList.remove('show');
                const form = emailForm.querySelector('form');
                if (form) form.reset();
            }
            
            if (slackForm) {
                slackForm.classList.remove('show');
                const form = slackForm.querySelector('form');
                if (form) form.reset();
            }
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html> 