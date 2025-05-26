<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])
            ->build();
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

    public function generateRule(Request $request)
    {
        // Get all the form data from query parameters
        $formData = [
            'rule_name' => $request->get('ruleName', ''),
            'elasticsearch_index' => $request->get('index', ''),
            'backing_index' => $request->get('backingIndex', ''),
            'alert_prompt' => $request->get('prompt', ''),
            'kql_syntax' => $request->get('kql', ''),
            'schedule_interval' => $request->get('interval', ''),
            'schedule_unit' => $request->get('unit', ''),
            'submitted_at' => now()->toDateTimeString(),
            
            // Email action fields
            'enable_email_action' => $request->boolean('enableEmailAction'),
            'email_recipient' => $request->get('emailRecipient', ''),
            'smtp_host' => $request->get('smtpHost', ''),
            'smtp_port' => $request->get('smtpPort', ''),
            'smtp_ssl' => $request->boolean('smtpSsl'),
            'from_address' => $request->get('fromAddress', ''),
            'smtp_username' => $request->get('smtpUsername', ''),
            'smtp_password' => $request->get('smtpPassword', '')
        ];

        // Create smtp_auth_file.txt if email action is enabled and credentials are provided
        $rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
        $smtpAuthFilePath = $rulesPath . DIRECTORY_SEPARATOR . 'smtp_auth_file.txt';
        $fileCreationMessage = null;

        if ($formData['enable_email_action']) {
            if (!empty($formData['smtp_username']) && !empty($formData['smtp_password'])) {
                $smtpAuthContent = "user: " . $formData['smtp_username'] . "\n";
                $smtpAuthContent .= "password: " . $formData['smtp_password'] . "\n";
                
                try {
                    if (!is_dir($rulesPath)) {
                        // mkdir($rulesPath, 0755, true);
                    }
                    file_put_contents($smtpAuthFilePath, $smtpAuthContent);
                    $fileCreationMessage = 'smtp_auth_file.txt created/updated successfully.';
                } catch (\Exception $e) {
                    $fileCreationMessage = 'Error creating smtp_auth_file.txt: ' . $e->getMessage();
                }
            }
        }

        // Return success response
        $responsePayload = [
            'success' => true,
            'message' => 'Alert rule generated successfully!',
            'received_data' => $formData 
        ];

        if ($formData['enable_email_action']) {
            $responsePayload['smtp_auth_file_path'] = $smtpAuthFilePath;
            if ($fileCreationMessage) {
                $responsePayload['smtp_auth_file_status'] = $fileCreationMessage;
            }
        } else {
             // If email action is not enabled, but we had a message (e.g. error from previous attempt if logic was different)
            if ($fileCreationMessage) {
                $responsePayload['smtp_auth_file_status'] = $fileCreationMessage; 
            }
        }

        return response()->json($responsePayload);
    }

    public function printRule(Request $request)
    {
        $outputData = [
            'Rule Name' => $request->get('ruleName', ''),
            'Elasticsearch Index (Display)' => $request->get('index', ''),
            // 'Elasticsearch Index (Backing)' => $request->get('backingIndex', ''),
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
            
            // Add smtp_auth_file path
            $rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
            $smtpAuthFilePath = $rulesPath . DIRECTORY_SEPARATOR . 'smtp_auth_file.txt';
            $output .= "SMTP Auth File: " . $smtpAuthFilePath . "\n";
        }

        $output .= "=================================";

        return response($output, 200)->header('Content-Type', 'text/plain');
    }

    public function showRulesPage()
    {
        // Logic to list rule files will be added here later
        $rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
        $ruleFiles = [];
        // Example: $ruleFiles = array_diff(scandir($rulesPath), array('.', '..')); 
        // We will implement robust file listing in the next step.

        return view('elasticsearch.rules', ['ruleFiles' => $ruleFiles, 'rulesPath' => $rulesPath]);
    }

    public function listRuleFiles()
    {
        $rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
        try {
            if (!is_dir($rulesPath) || !is_readable($rulesPath)) {
                return response()->json(['error' => 'Rules directory is not accessible.', 'path' => $rulesPath], 500);
            }
            $files = scandir($rulesPath);
            $yamlFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'yaml' || pathinfo($file, PATHINFO_EXTENSION) === 'yml';
            });
            return response()->json(array_values($yamlFiles)); // Return as a simple array of names
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to list rule files: ' . $e->getMessage()], 500);
        }
    }

    public function getRuleFileContent(Request $request)
    {
        $rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
        $fileName = $request->get('file');

        if (!$fileName) {
            return response()->json(['error' => 'File name parameter is required.'], 400);
        }

        // Basic sanitation to prevent directory traversal
        $fileName = basename($fileName);
        $filePath = $rulesPath . DIRECTORY_SEPARATOR . $fileName;

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'yaml' && pathinfo($filePath, PATHINFO_EXTENSION) !== 'yml') {
            return response()->json(['error' => 'Invalid file type. Only YAML files are allowed.'], 400);
        }

        try {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                return response()->json(['error' => 'Rule file not found or not readable.', 'path' => $filePath], 404);
            }
            $content = file_get_contents($filePath);
            return response($content, 200)->header('Content-Type', 'text/plain'); // Send as plain text
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to read rule file: ' . $e->getMessage(), 'path' => $filePath], 500);
        }
    }
} 