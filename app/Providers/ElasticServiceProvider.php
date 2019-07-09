<?php

namespace App\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        /**
         * You can also connect this using the following:
         * This ends up being much slower and caused ATAT to be 30ms slower on average
         */
        $this->app->singleton(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([
                    [
                        'host' => env('ELASTIC_HOST'),
                        'port' => env('ELASTIC_PORT'),
                        'scheme' => env('ELASTIC_SCHEME', 'http'),
                    ]
                ])->build();
        });
    }
}
