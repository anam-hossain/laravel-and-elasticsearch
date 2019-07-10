<?php

namespace App\Handlers;

use Elasticsearch\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class IndexHandler
{
    /**
     * Search index name
     */
    const INDEX_NAME = 'countries';

    /**
     * Search type
     */
    const TYPE = '_doc';

    /**
     * ElasticSearch client
     *
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * IndexHandler's constructor
     *
     * @param  \Elasticsearch\Client  $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create new index
     *
     * @return void
     */
    public function createIndex()
    {
        try {
            $isIndexExist = $this->client->indices()->exists(['index' => self::INDEX_NAME]);

            if (!$isIndexExist) {
                $this->client->indices()->create($this->params(self::INDEX_NAME));
            }
        } catch (Exception $e) {
            Log::error('Unable to create index', [
                'message' => $e->getMessage(),
                'index' => self::INDEX_NAME,
            ]);

            throw $e;
        }
    }

    /**
     * Index data
     *
     * @param integer $id
     * @param array $data
     * @return void
     */
    public function indexData($id, array $data)
    {
        try {
            $this->client->index([
                'index' => self::INDEX_NAME,
                'type' => self::TYPE,
                'id' => $id,
                'body' => $data,
            ]);
        } catch (Exception $e) {
            Log::critical('Indexing failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }

    /*
     * Remove search index
     *
     * @param string $index
     * @return array
     */
    public function removeIndex($index)
    {
        return $this->client->indices()->delete(['index' => $index]);
    }

    /**
     * Index params
     *
     * @return array
     */
    protected function params()
    {
        return [
            'index' => self::INDEX_NAME,
            'body' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'analysis' => [
                        'normalizer' => [
                            'normalizer_case_insensitive' => [
                                'type' => 'custom',
                                'filter' => [
                                    'lowercase',
                                ],
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    self::TYPE => [
                        '_source' => [
                            'enabled' => true,
                        ],
                        'properties' => [
                            'Name' => [
                                'type' => 'keyword',
                                'normalizer' => 'normalizer_case_insensitive',
                            ],
                            'LocalName' => [
                                'type' => 'keyword',
                                'normalizer' => 'normalizer_case_insensitive',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
