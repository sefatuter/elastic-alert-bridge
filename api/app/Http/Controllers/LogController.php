<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ElasticsearchService;

class LogController extends Controller
{
    private $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

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

    /**
     * Show indices explorer page
     */
    public function showIndices()
    {
        $indices = $this->elasticsearchService->getAllIndices();
        return view('logs.indices', compact('indices'));
    }

    /**
     * Search within a specific index
     */
    public function searchIndex(Request $request)
    {
        $indexName = $request->input('index');
        $searchQuery = $request->input('search', '');
        $page = (int) $request->input('page', 1);
        $size = 50;
        $from = ($page - 1) * $size;

        if (!$indexName) {
            return redirect()->route('logs.indices')->with('error', 'Please select an index');
        }

        $result = $this->elasticsearchService->searchInIndex($indexName, $searchQuery, $size, $from);
        
        $totalPages = ceil($result['total'] / $size);

        return view('logs.search', [
            'logs' => $result['hits'],
            'error' => $result['error'],
            'index' => $indexName,
            'searchQuery' => $searchQuery,
            'total' => $result['total'],
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'hasMore' => $page < $totalPages
        ]);
    }
} 