<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function index()
    {
        try {
            $query = [
                'size' => 100,
                'sort' => [
                    ['@timestamp' => 'desc']
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('http://localhost:9200/postgresql-logs-*/_search', $query);

            if (!$response->successful()) {
                Log::error('Elasticsearch request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return view('logs.index', [
                    'logs' => [], 
                    'error' => 'Failed to fetch logs from Elasticsearch. Status: ' . $response->status() . ' Response: ' . $response->body()
                ]);
            }

            $data = $response->json();
            
            if (!isset($data['hits']['hits'])) {
                Log::error('Unexpected Elasticsearch response format', ['response' => $data]);
                return view('logs.index', [
                    'logs' => [], 
                    'error' => 'Invalid response format from Elasticsearch'
                ]);
            }

            $logs = collect($data['hits']['hits'])
                ->map(function ($hit) {
                    return [
                        'timestamp' => $hit['_source']['@timestamp'] ?? '',
                        'message' => $hit['_source']['message'] ?? '',
                        'type' => $hit['_source']['type'] ?? '',
                        'file_path' => $hit['_source']['log']['file']['path'] ?? '',
                        'log_type' => str_contains($hit['_source']['log']['file']['path'] ?? '', '.csv') ? 'CSV' : 'LOG'
                    ];
                });

            return view('logs.index', compact('logs'));
        } catch (\Exception $e) {
            Log::error('Error in LogController', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('logs.index', [
                'logs' => [], 
                'error' => 'An error occurred while fetching logs: ' . $e->getMessage()
            ]);
        }
    }
} 