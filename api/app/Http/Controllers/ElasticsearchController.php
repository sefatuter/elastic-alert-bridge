<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ElasticsearchController extends Controller
{
    private $client;
    private $rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';

    public function __construct()
    {
        try {
            $this->client = ClientBuilder::create()
                ->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])
                ->build();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Elasticsearch client: ' . $e->getMessage());
            $this->client = null;
        }
    }

    public function index()
    {
        return view('elasticsearch.index');
    }

    public function createAlert()
    {
        return view('elasticsearch.create-alert');
    }

    public function getIndexes()
    {
        try {
            $response = $this->client->cat()->indices([
                'index' => '*',
                'format' => 'json',
                'h' => 'index,docs.count,store.size,health'
            ]);
            
            $indexes = collect($response->asArray())->map(function ($index) {
                $indexName = $index['index'];
                
                // Skip system indexes that start with .
                if (str_starts_with($indexName, '.') && !str_starts_with($indexName, '.ds-')) {
                    return null;
                }
                
                // For .ds- indexes, show the backing index
                if (str_starts_with($indexName, '.ds-')) {
                    $displayName = str_replace('.ds-', '', $indexName);
                    $displayName = preg_replace('/-\d{4}\.\d{2}\.\d{2}-\d+$/', '', $displayName);
                } else {
                    $displayName = $indexName;
                }
                
                return [
                    'name' => $indexName,
                    'display_name' => $displayName,
                    'docs_count' => intval($index['docs.count'] ?? 0),
                    'store_size' => $index['store.size'] ?? '0b',
                    'health' => $index['health'] ?? 'unknown'
                ];
            })->filter()->sortBy('display_name')->values();

            return response()->json($indexes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch indexes: ' . $e->getMessage()], 500);
        }
    }

    public function getIndexData(Request $request)
    {
        try {
            $indexName = $request->get('index');
            
            if (!$indexName) {
                return response()->json(['error' => 'Index name is required'], 400);
            }

            $searchParams = [
                'index' => $indexName,
                'body' => [
                    'size' => 100,
                    'query' => [
                        'match_all' => new \stdClass()
                    ]
                ]
            ];

            // Try to sort by @timestamp if it exists, otherwise no sort
            try {
                $mappingResponse = $this->client->indices()->getMapping(['index' => $indexName]);
                $mapping = $mappingResponse->asArray();
                $hasTimestamp = false;
                
                foreach ($mapping as $indexMapping) {
                    if (isset($indexMapping['mappings']['properties']['@timestamp'])) {
                        $hasTimestamp = true;
                        break;
                    }
                }
                
                if ($hasTimestamp) {
                    $searchParams['body']['sort'] = [
                        ['@timestamp' => ['order' => 'desc']]
                    ];
                }
            } catch (\Exception $e) {
                // If mapping fails, continue without sorting
            }

            $response = $this->client->search($searchParams);
            $responseArray = $response->asArray();
            
            $documents = collect($responseArray['hits']['hits'])->map(function ($hit) {
                return [
                    'id' => $hit['_id'],
                    'source' => $hit['_source'],
                    'timestamp' => $hit['_source']['@timestamp'] ?? ($hit['_source']['timestamp'] ?? null)
                ];
            });

            return response()->json([
                'total' => $responseArray['hits']['total']['value'] ?? count($documents),
                'documents' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch index data: ' . $e->getMessage()], 500);
        }
    }

    private function formatPromptForGemini(array $formData): string
    {
        $prompt = "Generate an Valid and Workable ElastAlert rule YAML content based on the following details include everything and do not leave empty space and fill it with what you have been given in a controlled way.:\n";
        $prompt .= "-------------------------------------\n";
        $prompt .= "Rule Name: " . ($formData['rule_name'] ?: 'N/A') . "\n";
        $prompt .= "Index: " . ($formData['elasticsearch_index'] ?: 'N/A') . "\n";
        $prompt .= "Schedule: Check every " . ($formData['schedule_interval'] ?: 'N/A') . " " . ($formData['schedule_unit'] ?: 'N/A') . "\n";
        $prompt .= "\nAlert Requirements (User Prompt):\n" . ($formData['alert_prompt'] ?: 'N/A') . "\n";
        if (!empty($formData['kql_syntax'])) {
            $prompt .= "\nKQL Syntax (User Provided):\n" . $formData['kql_syntax'] . "\n";
        }
        $prompt .= "-------------------------------------\n";

        if ($formData['enable_email_action']) {
            $prompt .= "\nEmail Action Details (if an email alert type is appropriate for the rule):\n";
            $prompt .= "Recipient Email(s): " . ($formData['email_recipient'] ?: 'N/A') . "\n";
            $prompt .= "SMTP Host: " . ($formData['smtp_host'] ?: 'N/A') . "\n";
            $prompt .= "SMTP Port: " . ($formData['smtp_port'] ?: 'N/A') . "\n";
            $prompt .= "SMTP SSL: " . ($formData['smtp_ssl'] ? 'Yes' : 'No') . "\n";
            $prompt .= "From Address: " . ($formData['from_address'] ?: 'N/A') . "\n";
            $prompt .= "SMTP Username: " . ($formData['smtp_username'] ?: 'N/A') . "\n";
            if (!empty($formData['smtp_username']) && !empty($formData['smtp_password'])) {
                 $prompt .= "Note: SMTP authentication is configured (username/password provided by user).You can use other SMTP context given you. The rule should use smtp_auth_file.\n";
            }
            $prompt .= "SMTP Auth File Path (for rule configuration): " . $this->rulesPath . DIRECTORY_SEPARATOR . 'smtp_auth_file.txt' . "\n";
        }
        $prompt .= "
            # ─────────────────────────────────────────────
            #  EXAMPLE YAML FILE CONTENT
            # ─────────────────────────────────────────────

            name: 
            type:                  
            index: 
            num_events: 
            timeframe:
                minutes: 

            filter:                         # sample match criteria
                - query_string:
                    query: ''

            alert:                          # choose email
                - email

            # ── ✉️  E-mail settings ──────────────────────
            email:                           # mandatory: main recipients
                - 


            smtp_host:
            smtp_port: 
            smtp_ssl: 
            smtp_auth_file:    # user/password YAML

            from_addr: 

            email_format: html              # html or omit for plain-text mail

            alert_subject: 'Alert: {0} events detected in {1}'
            alert_subject_args:
                - num_matches
                - rule.name

            alert_text_type: alert_text_only
            # Rewrite and fill the alert_text with the following format:
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
        ";
        $prompt .= "\n\nIMPORTANT INSTRUCTIONS:\n";
        $prompt .= "1. Generate only valid ElastAlert YAML content - no explanatory text before or after\n";
        $prompt .= "2. Use the specified index name in the rule\n";
        $prompt .= "3. ONLY use reliable ElastAlert variables in alert_text_args (rule.name, rule.index, num_matches, rule.timeframe, @timestamp, rule.type)\n";
        $prompt .= "4. DO NOT use custom variables like ALERT_LABEL, RULE_NAME, TIME_UNIT as they cause KeyError\n";
        $prompt .= "5. Make sure the number of placeholders {0}, {1}, etc. in alert_text matches the number of alert_text_args (exactly 6 arguments)\n";
        $prompt .= "6. Do not use '`' only give raw YAML output\n";
        $prompt .= "7. Ensure proper email formatting with clear sections and readable layout\n";
        $prompt .= "8. Use HTML-friendly formatting since email_format is set to html\n";
        $prompt .= "! Do not use '`' only give raw YAML output\n";

        return $prompt;
    }

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

        $geminiPrompt = $this->formatPromptForGemini($formData);
        
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

    public function printRule(Request $request)
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
            $output .= "Recipient Email(s): " . ($request->get('emailRecipient', '') ?: 'N/A') . "\n";
            $output .= "SMTP Host: " . ($request->get('smtpHost', '') ?: 'N/A') . "\n";
            $output .= "SMTP Port: " . ($request->get('smtpPort', '') ?: 'N/A') . "\n";
            $output .= "SMTP SSL: " . ($request->boolean('smtpSsl') ? 'Yes' : 'No') . "\n";
            $output .= "From Address: " . ($request->get('fromAddress', '') ?: 'N/A') . "\n";
            $output .= "SMTP Username: " . ($request->get('smtpUsername', '') ?: 'N/A') . "\n";
            $output .= "SMTP Password: " . ($request->get('smtpPassword') ? '[SET]' : 'N/A') . "\n";
            $smtpAuthFilePath = $this->rulesPath . DIRECTORY_SEPARATOR . 'smtp_auth_file.txt';
            $output .= "SMTP Auth File: " . $smtpAuthFilePath . "\n";
        }

        $output .= "=================================";
        return response($output, 200)->header('Content-Type', 'text/plain');
    }

    public function showRulesPage()
    {
        return view('elasticsearch.rules', ['rulesPath' => $this->rulesPath]);
    }

    public function listRuleFiles()
    {
        try {
            if (!is_dir($this->rulesPath) || !is_readable($this->rulesPath)) {
                return response()->json(['error' => 'Rules directory is not accessible.', 'path' => $this->rulesPath], 500);
            }
            $files = scandir($this->rulesPath);
            $yamlFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'yaml' || pathinfo($file, PATHINFO_EXTENSION) === 'yml';
            });
            return response()->json(array_values($yamlFiles));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to list rule files: ' . $e->getMessage()], 500);
        }
    }

    public function getRuleFileContent(Request $request)
    {
        $fileName = $request->get('file');
        if (!$fileName) {
            return response()->json(['error' => 'File name parameter is required.'], 400);
        }
        $fileName = basename($fileName);
        $filePath = $this->rulesPath . DIRECTORY_SEPARATOR . $fileName;

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'yaml' && pathinfo($filePath, PATHINFO_EXTENSION) !== 'yml') {
            return response()->json(['error' => 'Invalid file type. Only YAML files are allowed.'], 400);
        }

        try {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                return response()->json(['error' => 'Rule file not found or not readable.', 'path' => $filePath], 404);
            }
            $content = file_get_contents($filePath);
            return response($content, 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to read rule file: ' . $e->getMessage(), 'path' => $filePath], 500);
        }
    }
} 