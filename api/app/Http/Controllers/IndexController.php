<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\ElasticsearchService;

class IndexController extends Controller
{
    private $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    // Get all indexes from Elasticsearch
    public function getIndexes()
    {  
        $client = $this->elasticsearchService->getClient();

        if (!$client) {
            return response()->json(['error' => 'Unable to connect to Elasticsearch'], 500);
        }

        try {
            $response = $client->cat()->indices([
                'index' => '*',
                'format' => 'json',
                'h' => 'index,docs.count,store.size,health'
            ]);
            
            $indexes = collect($response->asArray())->map(function ($index) {
                $indexName = $index['index'];
                
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
        $client = $this->elasticsearchService->getClient();
        
        if (!$client) {
            return response()->json(['error' => 'Unable to connect to Elasticsearch'], 500);
        }
        
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

            try {
                $mappingResponse = $client->indices()->getMapping(['index' => $indexName]);
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

            $response = $client->search($searchParams);
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
}
