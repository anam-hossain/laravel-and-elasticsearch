<?php

namespace Tests\Unit\Handlers;

use App\Handlers\SearchHandler;
use Illuminate\Http\Request;
use Tests\TestCase;

class SearchHandlerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->searchHandler = $this->getSearchHandler();
    }

    /**
     * @test
     * @group SearchHandler
     */
    public function shouldSetPerPage()
    {
        $searchHandler = $this->searchHandler->setPerPage(20);

        $perPage = $this->getProtectedValue($searchHandler, 'perPage');

        $this->assertEquals(20, $perPage);
    }

    /**
     * @test
     * @group SearchHandler
     */
    public function shouldBuildSearchConditions()
    {
        $this->injectRequestData([
            'country' => 'Australia',
        ]);

        $searchHandler = $this->getSearchHandler();

        $this->invokeMethod($searchHandler, 'buildSearchConditions');

        $searchConditions = $this->getProtectedValue($searchHandler, 'searchConditions');

        $this->assertEquals($this->expectedSearchConditions('Name', 'Australia'), $searchConditions);
    }

    /**
     * @test
     * @group SearchHandler
     */
    public function shouldSearchCountries()
    {
        $this->injectRequestData([
            'country' => 'Australia',
        ]);

        $searchHandler = $this->getSearchHandler();

        $this->mockElasticClient(200, $this->elasticResponse());

        $response = $searchHandler->search();

        $expected = json_decode($this->elasticResponse(), true);

        $this->assertEquals($expected, $response);
    }


    /**
     * Get SearchHandler instance
     *
     * @return \App\Handlers\SearchHandler
     */
    protected function getSearchHandler()
    {
        return new SearchHandler($this->app->make(Request::class));
    }

    /**
     * Expected search conditions
     *
     * @param string $field
     * @param mixed $values
     * @return array
     */
    protected function expectedSearchConditions($field, $values)
    {
        $clause = 'must';

        $queries = [];

        if (is_array($values)) {
            $clause = 'should';

            foreach ($values as $value) {
                $queries[] = ['match' => [$field => strtolower($value)]];
            }
        } else {
            $queries[] = ['match' => [$field => strtolower($values)]];
        }

        return [[
            "bool" => [
                $clause => $queries,
            ],
        ]];
    }
}
