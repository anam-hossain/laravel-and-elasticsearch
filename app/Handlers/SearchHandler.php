<?php

namespace App\Handlers;

use Illuminate\Http\Request;
use Elasticsearch\Client;

class SearchHandler
{
    /**
     * The number of items to be shown per page
     *
     * @var integer
     */
    protected $perPage = 10;

    /**
     * Request fields to Elastic cluster fields mapping
     *
     * @var array
     */
    protected $mapFields = [
        'country' => 'Name',
        'continent' => 'Continent',
        'city' => 'cities.Name',
        'language' => 'languages.Language',
    ];

    /**
     * Search conditions
     *
     * @var array
     */
    protected $searchConditions = [];

    /**
     * @var Request
     */
    protected $request;

    /**
     * SearchHandler's constructor
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Set per page
     *
     * @param int $perPage
     * @return $this
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Search items
     *
     * @return array
     */
    public function search()
    {
        $this->buildSearchConditions();

        return $this->lookup();
    }

    /**
     * Build search conditions
     *
     * @return void
     */
    protected function buildSearchConditions()
    {
        $fields = array_keys($this->mapFields);

        foreach ($this->request->only($fields) as $field => $value) {
            $values = explode(';', strtolower($value));

            $matches = $this->buildMatches($field, $values);

            if (count($values) > 1) {
                // Assume query is country=Australia;England
                // Bool representation will be: country=Autralia OR country=England
                // Here, 'OR' is equivalent to 'should' in Elasticsearch
                $this->searchConditions[] = $this->buildShouldClause($matches);
            } else {
                $this->searchConditions[] = $this->buildMustClause($matches);
            }
        }
    }

    /**
     * Build matches
     *
     * @param string $field
     * @param array $values
     * @param int $boost
     * @return array
     */
    protected function buildMatches($field, array $values, $boost = 0)
    {
        $matches = [];

        foreach ($values as $value) {
            $query = $boost ? ['query' => $value, 'boost' => $boost] : $value;

            $matches[] = ['match' => [$this->mapFields[$field] => $query]];
        }

        return $matches;
    }

    /**
     * Build 'should' clause which is
     * equivalent to OR in sql. example: (make=toyota OR make=kia)
     *
     * @param array $queries
     * @return array
     */
    protected function buildShouldClause(array $queries)
    {
        return [
            'bool' => [
                'should' => $queries,
            ],
        ];
    }

    /**
     * Build 'must' clause which is equivalent to AND in sql.
     *
     * @param array $queries
     * @return array
     */
    protected function buildMustClause(array $queries)
    {
        return [
            'bool' => [
                'must' => $queries,
            ],
        ];
    }

    /**
     * Build 'must_not' clause which is equivalent to NOT in boolean.
     *
     * @param array $queries
     * @return array
     */
    protected function buildMustNotClause(array $queries)
    {
        return [
            'bool' => [
                'must_not' => $queries,
            ],
        ];
    }

    /**
     * Get Elastic params
     *
     * @return array
     */
    protected function getParams()
    {
        $params = [
            'index' => IndexHandler::INDEX_NAME,
            'type' => IndexHandler::TYPE,
            'size' => $this->perPage,
            'body' => [
                'query' => $this->getQuery(),
            ],
        ];

        return $params;
    }

    /**
     * Get query
     *
     * @return array
     */
    protected function getQuery()
    {
        $query = [
            'bool' => [
                'must' => $this->searchConditions,
            ],
        ];

        return $query;
    }

    /**
     * Find items in cluster
     *
     * @return array
     */
    protected function lookup()
    {
        $result = ['hits' => ['total' => 0]];

        $params = $this->getParams();

        try {
            $result = app(Client::class)->search($params);
        } catch (Exception $e) {
            Log::critical('Search failed', [
                'message' => $e->getMessage(),
                'index' => $params['index'],
                'params' => $params,
            ]);
        }

        return $result;
    }
}
