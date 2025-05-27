<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    // Prepare prompt for Gemini
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

        // Get email information if email action is enabled
        // TODO: get email information from profile integrations

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
              query: "{$kqlSyntax}"
        
        alert:
          - email
        
        # ── ✉️  E-mail settings ──────────────────────
        email:
          - {$formData['email_recipient'] ?? 'recipient@example.com'}
        
        smtp_host: {$formData['smtp_host'] ?? 'smtp.example.com'}
        smtp_port: {$formData['smtp_port'] ?? '587'}
        smtp_ssl: {(($formData['smtp_ssl'] ?? false) ? 'true' : 'false')}
        smtp_auth_file: "{$this->rulesPath}/smtp_auth_file.txt"
        
        from_addr: {$formData['from_address'] ?? 'noreply@example.com'}
        
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
        
          \n
            - Error Message: {6}
        
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
        6. Do not use '`' only give raw YAML output
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
        Recipient Email(s): {$formData['email_recipient'] ?? 'N/A'}
        SMTP Host: {$formData['smtp_host'] ?? 'N/A'}
        SMTP Port: {$formData['smtp_port'] ?? 'N/A'}
        SMTP SSL: {(($formData['smtp_ssl'] ?? false) ? 'Yes' : 'No')}
        From Address: {$formData['from_address'] ?? 'N/A'}
        SMTP Username: {$smtpUsername}
        {$authNote}SMTP Auth File Path (for rule configuration): {$this->rulesPath}/smtp_auth_file.txt
        
        EMAIL;
        }

        return $prompt;
    }

    //
    public function generateRule(Request $request)
    {
        $formData = [
            'rule_name' => $request->get('ruleName', ''),
            'elasticsearch_index' => $request->get('index', ''),
            // 'backing_index' => $request->get('backingIndex', ''),
            'alert_prompt' => $request->get('prompt', ''),
            'kql_syntax' => $request->get('kql', ''),
            'schedule_interval' => $request->get('interval', '5'),
            'schedule_unit' => $request->get('unit', 'minutes'),
            'submitted_at' => now()->toDateTimeString(),
            'enable_email_action' => $request->boolean('enableEmailAction'),
            'email_recipient' => $request->get('emailRecipient', ''),
            'smtp_host' => $request->get('smtpHost', ''),
            'smtp_port' => $request->get('smtpPort', ''),
            'smtp_ssl' => $request->boolean('smtpSsl'),
            'from_address' => $request->get('fromAddress', ''),
            'smtp_username' => $request->get('smtpUsername', ''),
            'smtp_password' => $request->get('smtpPassword', '')
        ];

        if (empty($formData['rule_name'])) {
            return response()->json(['success' => false, 'error' => 'Rule Name is required.'], 400);
        }
        if (empty($formData['alert_prompt'])) {
            return response()->json(['success' => false, 'error' => 'Alert Requirements (prompt) is required.'], 400);
        }

        $geminiPrompt = $this->prepareEmailPrompt($formData);
        
        Log::info("Gemini Prompt for rule '{$formData['rule_name']}':\n{$geminiPrompt}");

        $apiKey = '';
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
                    return response()->json(['success' => false, 'error' => 'Gemini API returned an empty response.', 'details' => $responseData], 500);
                }
                Log::info("Gemini Response for rule '{$formData['rule_name']}':\n{$generatedYamlContent}");
            } else {
                Log::error('Gemini API call failed.', ['status' => $apiResponse->status(), 'response' => $apiResponse->body()]);
                return response()->json(['success' => false, 'error' => 'Gemini API call failed.', 'details' => $apiResponse->json() ?: $apiResponse->body()], $apiResponse->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini API Connection Exception.', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Could not connect to Gemini API. ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('General Exception calling Gemini API.', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred while calling Gemini API: ' . $e->getMessage()], 500);
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
            $yamlFileStatus = "Error creating {$yamlFileName}: " . $e->getMessage();
            return response()->json(['success' => false, 'error' => $yamlFileStatus, 'gemini_output' => $generatedYamlContent], 500);
        }

        $smtpAuthFilePath = $this->rulesPath . DIRECTORY_SEPARATOR . 'smtp_auth_file.txt';
        $smtpAuthFileStatus = null;
        if ($formData['enable_email_action'] && !empty($formData['smtp_username']) && !empty($formData['smtp_password'])) {
            $smtpAuthContent = "user: " . $formData['smtp_username'] . "\npassword: " . $formData['smtp_password'] . "\n";
            try {
                file_put_contents($smtpAuthFilePath, $smtpAuthContent);
                $smtpAuthFileStatus = 'smtp_auth_file.txt created/updated.';
            } catch (\Exception $e) {
                Log::error("Failed to write smtp_auth_file.txt", ['message' => $e->getMessage()]);
                $smtpAuthFileStatus = 'Error creating smtp_auth_file.txt: ' . $e->getMessage();
            }
        }

        $redirectUrl = route('elasticsearch.rules', ['selected_file' => $yamlFileName]);
        
        $responsePayload = [
            'success' => true,
            'message' => 'ElastAlert rule generated by AI and saved as ' . $yamlFileName . '!',
            'redirect_url' => $redirectUrl,
            'rule_file' => $yamlFileName,
            'yaml_file_status' => $yamlFileStatus
        ];
        if ($smtpAuthFileStatus) {
            $responsePayload['smtp_auth_file_status'] = $smtpAuthFileStatus;
            $responsePayload['smtp_auth_file_path'] = $smtpAuthFilePath;
        }

        return response()->json($responsePayload);
    }

    
}
