<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\EmailIntegration;
use App\Models\SlackIntegration;

class AIController extends Controller 
{
    private $rulesPath;
    private $integrationAuthPath;

    public function __construct()
    {
        $this->rulesPath = storage_path('app/elastalert2/rules');
        $this->integrationAuthPath = storage_path('app/elastalert2/rules');
    }

    private function findIntegrationAuthFile($integrationId)
    {
        if (!$integrationId) {
            return null;
        }
        
        // Use the integration ID as the file number for a direct mapping
        $authFileName = "smtp_auth_file{$integrationId}.txt";
        $authFilePath = $this->integrationAuthPath . DIRECTORY_SEPARATOR . $authFileName;
        
        // Check if the file exists
        if (file_exists($authFilePath)) {
            return $authFileName;
        }
        
        return null;
    }

    private function prepareEmailPrompt(array $formData): string
    {
        // Get form data
        $ruleName = $formData['rule_name'] ?? 'N/A';
        $index = $formData['elasticsearch_index'] ?? 'N/A';
        $scheduleInterval = $formData['schedule_interval'] ?? 'N/A';
        $scheduleUnit = $formData['schedule_unit'] ?? 'N/A';
        $alertPrompt = $formData['alert_prompt'] ?? 'N/A';
        $kqlSyntax = $formData['kql_syntax'] ?? null;

        $prompt = <<<PROMPT
            Generate a valid and workable ElastAlert rule YAML content based on the following details. 
            Include everything and do not leave empty space; fill it with what you have been given in a controlled way.
            -------------------------------------
            Rule Name: {$ruleName}
            Index: {$index}
            Schedule: Check every {$scheduleInterval} {$scheduleUnit}

            Alert Requirements (User Prompt):
            {$alertPrompt}
            PROMPT;

        if (!empty($kqlSyntax)) {
            $prompt .= "\nKQL Syntax (User Provided):\n{$kqlSyntax}";
        }

        // Determine the correct SMTP auth file path
        $smtpAuthFilePath = $this->rulesPath . '/smtp_auth_file.txt'; // Default for custom
        
        if (isset($formData['email_type']) && $formData['email_type'] === 'integration' && isset($formData['integration_id'])) {
            $integrationAuthFile = $this->findIntegrationAuthFile($formData['integration_id']);
            if ($integrationAuthFile) {
                $smtpAuthFilePath = $this->integrationAuthPath . '/' . $integrationAuthFile;
            }
        }

        // Prepare variables for the YAML template
        $emailRecipient = $formData['email_recipient'] ?? 'recipient@example.com';
        $smtpHost = $formData['smtp_host'] ?? 'smtp.example.com';
        $smtpPort = $formData['smtp_port'] ?? '587';
        $smtpSsl = ($formData['smtp_ssl'] ?? false) ? 'true' : 'false';
        $fromAddress = $formData['from_address'] ?? 'noreply@example.com';
        $queryString = $kqlSyntax ?: '*';

        $prompt .= <<<YAML


        # ─────────────────────────────────────────────
        #  EXAMPLE YAML FILE CONTENT
        # ─────────────────────────────────────────────
        
        name: {$ruleName}
        type: any
        index: {$index}
        num_events: 1
        timeframe:
          minutes: 5
        
        filter:
          - query_string:
              query: "{$queryString}"
        
        alert:
          - email
        
        # ── ✉️  E-mail settings ──────────────────────
        email:
          - {$emailRecipient}
        
        smtp_host: {$smtpHost}
        smtp_port: {$smtpPort}
        smtp_ssl: {$smtpSsl}
        smtp_auth_file: "{$smtpAuthFilePath}"
        
        from_addr: {$fromAddress}
        
        email_format: html
        
        alert_subject: 'Alert: {0} events detected in {1}'
        alert_subject_args:
          - num_matches
          - rule.name
        
        alert_text_type: alert_text_only
        alert_text: |
          🚨 ELASTICSEARCH ALERT
        
          Alert Details:
          ─────────────────────────────────
          Rule: {0}
          Index: {1}
          Events Detected: {2}
          Time Range: {3}
        
          📊 Event Information:
          • Total Matches: {2}
          • Latest Event Time: {4}
          • Rule Type: {5}
          - Error: {6}
        
          🔍 Investigation Links:
          ↗️ Check Kibana Dashboard
          ↗️ View Raw Logs in Elasticsearch
        
        alert_text_args:
          - rule.name
          - rule.index
          - num_matches
          - rule.timeframe
          - '@timestamp'
          - rule.type
          - message
        
        YAML;
        
        $prompt .= <<<INFO
        
        IMPORTANT INSTRUCTIONS:
        1. Generate only valid ElastAlert YAML content - no explanatory text before or after
        2. Use the specified index name in the rule
        3. ONLY use reliable ElastAlert variables in alert_text_args (rule.name, rule.index, num_matches, rule.timeframe, @timestamp, rule.type)
        4. DO NOT use custom variables like ALERT_LABEL, RULE_NAME, TIME_UNIT as they cause KeyError
        5. Make sure the number of placeholders {0}, {1}, etc. in alert_text matches the number of alert_text_args (exactly 6 arguments)
        6. Do not use backticks, only give raw YAML output
        7. Ensure proper email formatting with clear sections and readable layout
        8. Use HTML-friendly formatting since email_format is set to html
        9. Ensure no unnecessary fields are left blank
        
        INFO;
        
        if ($formData['enable_email_action']) {
            $smtpUsername = $formData['smtp_username'] ?? 'N/A';
            $smtpPassword = $formData['smtp_password'] ?? '';
            $authNote = (!empty($smtpUsername) && !empty($smtpPassword)) 
                ? "Note: SMTP authentication is configured (username/password provided by user). You can use other SMTP context given to you. The rule should use smtp_auth_file.\n"
                : "";
        
            $prompt .= <<<EMAIL
        
        Email Action Details (if an email alert type is appropriate for the rule):
        Recipient Email(s): {$emailRecipient}
        SMTP Host: {$smtpHost}
        SMTP Port: {$smtpPort}
        SMTP SSL: {$smtpSsl}
        From Address: {$fromAddress}
        SMTP Username: {$smtpUsername}
        {$authNote}SMTP Auth File Path (for rule configuration): {$smtpAuthFilePath}
        
        EMAIL;
        }

        return $prompt;
    }

    private function prepareSlackPrompt(array $formData): string
    {
        // Get form data
        $ruleName = $formData['rule_name'] ?? 'N/A';
        $index = $formData['elasticsearch_index'] ?? 'N/A';
        $scheduleInterval = $formData['schedule_interval'] ?? 'N/A';
        $scheduleUnit = $formData['schedule_unit'] ?? 'N/A';
        $alertPrompt = $formData['alert_prompt'] ?? 'N/A';
        $kqlSyntax = $formData['kql_syntax'] ?? null;

        $prompt = <<<PROMPT
            Generate a valid and workable ElastAlert rule YAML content based on the following details. 
            Include everything and do not leave empty space; fill it with what you have been given in a controlled way.
            -------------------------------------
            Rule Name: {$ruleName}
            Index: {$index}
            Schedule: Check every {$scheduleInterval} {$scheduleUnit}

            Alert Requirements (User Prompt):
            {$alertPrompt}
            PROMPT;

        if (!empty($kqlSyntax)) {
            $prompt .= "\nKQL Syntax (User Provided):\n{$kqlSyntax}";
        }

        // Prepare variables for the YAML template
        $webhookUrl = $formData['webhook_url'] ?? 'https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK';
        $channel = $formData['channel'] ?? '#alerts';
        $username = $formData['username'] ?? 'ElastAlert';
        $iconEmoji = $formData['icon_emoji'] ?? ':warning:';
        $queryString = $kqlSyntax ?: '*';

                $prompt .= <<<YAML


        # ─────────────────────────────────────────────
        #  EXAMPLE YAML FILE CONTENT
        # ─────────────────────────────────────────────
        
        name: {$ruleName}
        type: any
        index: {$index}
        num_events: 1
        timeframe:
          minutes: 5
        
        filter:
          - query_string:
              query: "{$queryString}"
        
        alert:
          - slack
        
        # ── 💬 Slack settings ──────────────────────
        slack_webhook_url: "{$webhookUrl}"
        slack_channel_override: "{$channel}"
        slack_username_override: "{$username}"
        slack_emoji_override: "{$iconEmoji}"
        slack_msg_color: "danger"
        
        slack_title: "🚨 ElastAlert: {0}"
        slack_title_args:
          - rule.name
        
        alert_subject: "Alert triggered for rule {0}"
        alert_subject_args:
          - rule.name
        
        alert_text: |
          Rule: {0}
          Index: {1}
          Events: {2}
          Time: {3}
          
          Details:
          - Rule Name: {0}
          - Elasticsearch Index: {1}
          - Number of Events: {2}
          - Event Timestamp: {3}
          
          Action Required: Check logs and investigate the alert condition.
        
        alert_text_args:
          - rule.name
          - rule.index
          - num_matches
          - '@timestamp'
        
        YAML;
        
        $prompt .= <<<INFO
        
        CRITICAL ELASTALERT SLACK YAML REQUIREMENTS:
        1. Generate ONLY valid ElastAlert YAML - no explanations, no comments with #
        2. Use SINGLE curly braces {0}, {1}, {2}, {3} for ALL placeholders - NOT double braces
        3. All Slack configuration goes at ROOT level - do NOT nest under 'slack:' section
        4. Use these EXACT field names for Slack:
           - slack_webhook_url (required)
           - slack_channel_override (optional)
           - slack_username_override (optional) 
           - slack_emoji_override (optional)
           - slack_msg_color (optional: "danger", "good", "warning")
           - slack_title (optional)
           - slack_title_args (optional)
        5. For message content, use alert_text and alert_text_args (NOT slack_text)
        6. Use EXACTLY 4 placeholders: {0}, {1}, {2}, {3} in alert_text
        7. Use EXACTLY 4 items in alert_text_args: rule.name, rule.index, num_matches, '@timestamp'
        8. Placeholders must match: {0}=rule.name, {1}=rule.index, {2}=num_matches, {3}='@timestamp'
        9. Keep alert_text simple - avoid complex Slack markdown that may break
        10. Output pure YAML only - no backticks, no explanations before or after
        
        INFO;
        
        if ($formData['enable_slack_action']) {
            $prompt .= <<<SLACK
        
        Slack Action Details:
        Webhook URL: {$webhookUrl}
        Channel: {$channel}
        Username: {$username}
        Icon Emoji: {$iconEmoji}
        
        IMPORTANT REMINDERS:
        - Use single curly braces {0}, {1}, {2}, {3} for placeholders in YAML output
        - DO NOT use double curly braces {{0}} - ElastAlert requires single braces
        - Use alert_text and alert_text_args for message content (NOT slack_text)
        - All Slack config fields go at root level, not nested under 'slack:'
        
        SLACK;
        }

        return $prompt;
    }

    //
    public function generateRule(Request $request)
    {
        // Get basic form data - map form field names to expected names
        $formData = [
            'rule_name' => $request->get('ruleName', ''),
            'elasticsearch_index' => $request->get('index', ''),
            'alert_prompt' => $request->get('prompt', ''),
            'kql_syntax' => $request->get('kql', ''),
            'schedule_interval' => $request->get('interval', '5'),
            'schedule_unit' => $request->get('unit', 'minutes'),
            'submitted_at' => now()->toDateTimeString(),
            'enable_email_action' => $request->boolean('enableEmailAction') || $request->get('selectedAction') === 'email',
            'enable_slack_action' => $request->boolean('enableSlackAction') || $request->get('selectedAction') === 'slack',
        ];

        // Handle email configuration based on type
        if ($formData['enable_email_action']) {
            $emailType = $request->get('emailType', 'custom');
            $formData['email_type'] = $emailType;
            
            if ($emailType === 'integration') {
                // Get email configuration from database integration
                $integrationId = $request->get('integrationId');
                
                if (empty($integrationId)) {
                    return back()->withErrors(['error' => 'Integration ID is required when using email integration.'])->withInput();
                }
                
                $integration = EmailIntegration::find($integrationId);
                
                if (!$integration) {
                    return back()->withErrors(['error' => 'Email integration not found.'])->withInput();
                }
                
                $formData['integration_id'] = $integrationId;
                // Use integrationEmailRecipient field for integration type, fallback to default
                $formData['email_recipient'] = $request->get('integrationEmailRecipient', $integration->default_recipient ?? '');
                $formData['smtp_host'] = $integration->smtp_host;
                $formData['smtp_port'] = $integration->smtp_port;
                $formData['smtp_ssl'] = $integration->smtp_ssl;
                $formData['from_address'] = $integration->from_address;
                $formData['smtp_username'] = $integration->smtp_username;
                $formData['smtp_password'] = $integration->smtp_password;
                $formData['integration_name'] = $integration->name;
            } else {
                // Use custom email configuration
                $formData['email_recipient'] = $request->get('emailRecipient', '');
                $formData['smtp_host'] = $request->get('smtpHost', '');
                $formData['smtp_port'] = $request->get('smtpPort', '');
                $formData['smtp_ssl'] = $request->boolean('smtpSsl');
                $formData['from_address'] = $request->get('fromAddress', '');
                $formData['smtp_username'] = $request->get('smtpUsername', '');
                $formData['smtp_password'] = $request->get('smtpPassword', '');
            }
            
            // Validate email recipient is provided
            if (empty($formData['email_recipient'])) {
                return back()->withErrors(['error' => 'Email recipient is required when email action is enabled.'])->withInput();
            }
        }

        // Handle Slack configuration based on type
        if ($formData['enable_slack_action']) {
            $slackType = $request->get('slackType', 'custom');
            $formData['slack_type'] = $slackType;
            
            if ($slackType === 'integration') {
                // Get Slack configuration from database integration
                $slackIntegrationId = $request->get('slackIntegrationId');
                
                if (empty($slackIntegrationId)) {
                    return back()->withErrors(['error' => 'Slack Integration ID is required when using Slack integration.'])->withInput();
                }
                
                $slackIntegration = SlackIntegration::find($slackIntegrationId);
                
                if (!$slackIntegration) {
                    return back()->withErrors(['error' => 'Slack integration not found.'])->withInput();
                }
                
                $formData['slack_integration_id'] = $slackIntegrationId;
                $formData['webhook_url'] = $slackIntegration->webhook_url;
                $formData['channel'] = $slackIntegration->channel;
                $formData['username'] = $slackIntegration->username;
                $formData['icon_emoji'] = $slackIntegration->icon_emoji;
                $formData['slack_integration_name'] = $slackIntegration->name;
            } else {
                // Use custom Slack configuration
                $formData['webhook_url'] = $request->get('slackWebhookUrl', '');
                $formData['channel'] = $request->get('slackChannel', '');
                $formData['username'] = $request->get('slackUsername', '');
                $formData['icon_emoji'] = $request->get('slackIconEmoji', '');
            }
            
            // Validate webhook URL is provided
            if (empty($formData['webhook_url'])) {
                return back()->withErrors(['error' => 'Slack webhook URL is required when Slack action is enabled.'])->withInput();
            }
        }

        if (empty($formData['rule_name'])) {
            return back()->withErrors(['error' => 'Rule Name is required.'])->withInput();
        }
        if (empty($formData['alert_prompt'])) {
            return back()->withErrors(['error' => 'Alert Requirements (prompt) is required.'])->withInput();
        }

        // Determine which prompt to use based on action type
        if ($formData['enable_slack_action']) {
            $geminiPrompt = $this->prepareSlackPrompt($formData);
        } else {
            $geminiPrompt = $this->prepareEmailPrompt($formData);
        }
        
        Log::info("Gemini Prompt for rule '{$formData['rule_name']}':\n{$geminiPrompt}");

        // Get API key from environment or config
        $apiKey = env('GEMINI_API_KEY', config('services.gemini.api_key', ''));
        
        if (empty($apiKey)) {
            Log::error('Gemini API key is not configured');
            return back()->withErrors(['error' => 'AI service is not properly configured. Please contact administrator.'])->withInput();
        }
        
        $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;
        $generatedYamlContent = null;

        try {
            $apiResponse = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($geminiApiUrl, [
                    'contents' => [['parts' => [['text' => $geminiPrompt]]]],
                ]);

            if ($apiResponse->successful()) {
                $responseData = $apiResponse->json();
                $generatedYamlContent = data_get($responseData, 'candidates.0.content.parts.0.text');
                if (empty($generatedYamlContent)) {
                    Log::error('Gemini API success but no content generated.', ['response' => $responseData]);
                    return back()->withErrors(['error' => 'AI service returned an empty response. Please try again.'])->withInput();
                }
                Log::info("Gemini Response for rule '{$formData['rule_name']}':\n{$generatedYamlContent}");
            } else {
                Log::error('Gemini API call failed.', ['status' => $apiResponse->status(), 'response' => $apiResponse->body()]);
                return back()->withErrors(['error' => 'AI service is currently unavailable. Please try again later.'])->withInput();
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini API Connection Exception.', ['message' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Could not connect to AI service. Please check your internet connection.'])->withInput();
        } catch (\Exception $e) {
            Log::error('General Exception calling Gemini API.', ['message' => $e->getMessage()]);
            return back()->withErrors(['error' => 'An unexpected error occurred while generating the rule. Please try again.'])->withInput();
        }

        $safeRuleName = Str::slug($formData['rule_name']);
        $yamlFileName = $safeRuleName . '.yaml';
        $yamlFilePath = $this->rulesPath . DIRECTORY_SEPARATOR . $yamlFileName;
        $yamlFileStatus = null;

        try {
            if (!is_dir($this->rulesPath)) {
                mkdir($this->rulesPath, 0755, true);
            }
            file_put_contents($yamlFilePath, $generatedYamlContent);
            $yamlFileStatus = $yamlFileName . ' created successfully.';
        } catch (\Exception $e) {
            Log::error("Failed to write YAML rule file: {$yamlFileName}", ['message' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to save the rule file. Please check file permissions.'])->withInput();
        }

        $smtpAuthFilePath = $this->rulesPath . DIRECTORY_SEPARATOR . 'smtp_auth_file.txt';
        $smtpAuthFileStatus = null;
        if ($formData['enable_email_action'] && !empty($formData['smtp_username']) && !empty($formData['smtp_password'])) {
            // For custom email configuration, create temporary auth file
            if ($formData['email_type'] === 'custom') {
                $smtpAuthContent = "user: " . $formData['smtp_username'] . "\npassword: " . $formData['smtp_password'] . "\n";
                try {
                    file_put_contents($smtpAuthFilePath, $smtpAuthContent);
                    $smtpAuthFileStatus = 'smtp_auth_file.txt created/updated.';
                } catch (\Exception $e) {
                    Log::error("Failed to write smtp_auth_file.txt", ['message' => $e->getMessage()]);
                    $smtpAuthFileStatus = 'Error creating smtp_auth_file.txt: ' . $e->getMessage();
                }
            } else {
                // For integration, the auth file already exists, just log the usage
                $integrationAuthFile = $this->findIntegrationAuthFile($formData['integration_id']);
                if ($integrationAuthFile) {
                    $smtpAuthFileStatus = "Using integration auth file: {$integrationAuthFile}";
                } else {
                    $smtpAuthFileStatus = 'Warning: Integration auth file not found.';
                }
            }
        }

        // Success - redirect to rules page with success message
        $redirectUrl = route('elasticsearch.rules', ['selected_file' => $yamlFileName]);
        $successMessage = 'ElastAlert rule "' . $formData['rule_name'] . '" generated successfully and saved as ' . $yamlFileName . '!';
        
        return redirect($redirectUrl)->with('success', $successMessage);
    }

    
}
