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
            $hosts = [env('ELASTICSEARCH_HOST', 'localhost:9200')];

            $username = env('ELASTICSEARCH_USERNAME', null);
            $password = env('ELASTICSEARCH_PASSWORD', null);

            $builder = ClientBuilder::create()
                ->setHosts($hosts);

            if ($username !== null && $password !== null) {
                $builder->setBasicAuthentication($username, $password);
            }

            $this->client = $builder->build();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Elasticsearch client: ' . $e->getMessage());
            $this->client = null;
        }
    }

    /**
     * Elasticsearch client
     *
     * @return \Elastic\Elasticsearch\Client|null
     */
    public function getClient()
    {
        return $this->client;
    }
}
