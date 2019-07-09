<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchConrollerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->mockElasticClient();
    }

    /**
     * @test
     * @group SearchController
     */
    public function shouldSearchItems()
    {
        $response = $this->json('GET', '/search', ['country' => 'Australia']);

        $response->assertStatus(200)
            ->assertJson([
                'hits' => [
                    'total' => 1,
                ],
            ]);
    }
}
