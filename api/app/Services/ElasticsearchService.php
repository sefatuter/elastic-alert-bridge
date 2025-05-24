<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    private $elasticsearchUrl;

    public function __construct()
    {
        $this->elasticsearchUrl = 'http://localhost:9200';
    }

    /**
     * Get all indices from Elasticsearch
     */
    public function getAllIndices()
    {
        try {
            $response = Http::get($this->elasticsearchUrl . '/_cat/indices?format=json&h=index,docs.count,store.size,health,status');

            if (!$response->successful()) {
                Log::error('Failed to fetch indices from Elasticsearch', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $indices = $response->json();
            
            // Mark system indices but don't filter them out
            $allIndices = collect($indices)
                ->map(function ($index) {
                    $index['is_system'] = str_starts_with($index['index'], '.');
                    return $index;
                })
                ->sortBy('index')
                ->values()
                ->all();

            return $allIndices;
        } catch (\Exception $e) {
            Log::error('Error fetching indices', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Search within a specific index
     */
    public function searchInIndex($indexName, $searchQuery = '', $size = 100, $from = 0)
    {
        try {
            $query = [
                'size' => $size,
                'from' => $from,
                'sort' => [
                    ['@timestamp' => ['order' => 'desc', 'missing' => '_last']]
                ]
            ];

            // If search query is provided, add it to the query
            if (!empty($searchQuery)) {
                $query['query'] = [
                    'query_string' => [
                        'query' => '*' . $searchQuery . '*',
                        'default_operator' => 'AND'
                    ]
                ];
            } else {
                $query['query'] = ['match_all' => (object)[]];
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->elasticsearchUrl . '/' . $indexName . '/_search', $query);

            if (!$response->successful()) {
                Log::error('Elasticsearch search request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'index' => $indexName,
                    'query' => $searchQuery
                ]);
                return [
                    'hits' => [],
                    'total' => 0,
                    'error' => 'Failed to search in index: ' . $indexName
                ];
            }

            $data = $response->json();
            
            if (!isset($data['hits']['hits'])) {
                return [
                    'hits' => [],
                    'total' => 0,
                    'error' => 'Invalid response format from Elasticsearch'
                ];
            }

            $hits = collect($data['hits']['hits'])
                ->map(function ($hit) {
                    $source = $hit['_source'] ?? [];
                    return [
                        'id' => $hit['_id'] ?? '',
                        'timestamp' => $source['@timestamp'] ?? $source['timestamp'] ?? '',
                        'message' => $source['message'] ?? json_encode($source),
                        'source' => $source,
                        'index' => $hit['_index'] ?? ''
                    ];
                });

            $total = $data['hits']['total']['value'] ?? count($hits);

            return [
                'hits' => $hits->all(),
                'total' => $total,
                'error' => null
            ];
        } catch (\Exception $e) {
            Log::error('Error searching in index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'index' => $indexName,
                'query' => $searchQuery
            ]);
            
            return [
                'hits' => [],
                'total' => 0,
                'error' => 'Search error: ' . $e->getMessage()
            ];
        }
    }
} 