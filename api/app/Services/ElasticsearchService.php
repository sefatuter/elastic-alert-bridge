<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    private $client;

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

    public function getClient()
    {
        return $this->client;
    }
}