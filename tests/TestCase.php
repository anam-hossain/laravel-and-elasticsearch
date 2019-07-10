<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;
use GuzzleHttp\Ring\Client\MockHandler;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Inject data to request object
     *
     * @param array $input
     * @return void
     */
    protected function injectRequestData($input)
    {
        $request = (new Request())->replace($input);

        $this->app->instance(Request::class, $request);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     * @return mixed Method return.
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Get protected property value
     *
     * @param $object
     * @param string $property
     * @return void
     * @throws \ReflectionException
     */
    protected function getProtectedValue($object, $property)
    {
        $reflection = (new \ReflectionClass($object))->getProperty($property);

        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    /**
     * Sets a protected property on a given object via reflection
     *
     * @param $object - instance in which protected value is being modified
     * @param $property - property on instance being modified
     * @param $value - new value of the property being modified
     * @return void
     * @throws \ReflectionException
     */
    public function setProtectedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }

    /**
    * Mock elastic client
    *
    * @param integer $status
    * @param string $response
    * @return void
    */
    protected function mockElasticClient(int $status = 200, string $response = null)
    {
        $response = $response ?? $this->elasticResponse();

        // The connection class requires 'body' to be a file stream handle
        // Depending on what kind of request you do, you may need to set more values here
        $handler = new MockHandler([
            'status' => $status,
            'transfer_stats' => [
                'total_time' => 100,
            ],
            'body' => fopen('data://text/plain,' . $response, 'r'),
            'effective_url' => 'localhost',
        ]);

        $builder = ClientBuilder::create();
        $builder->setHosts(['somehost'])->setHandler($handler);

        $client = $builder->build();

        $this->app->instance(Client::class, $client);
    }

    /**
     * ElasticSearch response
     *
     * @return void
     */
    protected function elasticResponse()
    {
        return '{
            "took": 10,
            "timed_out": false,
            "_shards": {
                "total": 2,
                "successful": 2,
                "skipped": 0,
                "failed": 0
            },
            "hits": {
                "total": 1,
                "max_score": 4.3652196,
                "hits": [
                {
                    "_index": "countries",
                    "_type": "_doc",
                    "_id": "AUS",
                    "_score": 4.3652196,
                    "_source": {
                    "Code": "AUS",
                    "Name": "Australia",
                    "Continent": "Oceania",
                    "Region": "Australia and New Zealand",
                    "SurfaceArea": 7741220,
                    "IndepYear": 1901,
                    "Population": 18886000,
                    "LifeExpectancy": 79.8,
                    "GNP": 351182,
                    "GNPOld": 392911,
                    "LocalName": "Australia",
                    "GovernmentForm": "Constitutional Monarchy, Federation",
                    "HeadOfState": "Elisabeth II",
                    "Capital": 135,
                    "Code2": "AU",
                    "cities": [
                        {
                            "ID": 130,
                            "Name": "Sydney",
                            "CountryCode": "AUS",
                            "District": "New South Wales",
                            "Population": 3276207
                        },
                        {
                            "ID": 131,
                            "Name": "Melbourne",
                            "CountryCode": "AUS",
                            "District": "Victoria",
                            "Population": 2865329
                        },
                        {
                            "ID": 132,
                            "Name": "Brisbane",
                            "CountryCode": "AUS",
                            "District": "Queensland",
                            "Population": 1291117
                        },
                        {
                            "ID": 133,
                            "Name": "Perth",
                            "CountryCode": "AUS",
                            "District": "West Australia",
                            "Population": 1096829
                        },
                        {
                            "ID": 134,
                            "Name": "Adelaide",
                            "CountryCode": "AUS",
                            "District": "South Australia",
                            "Population": 978100
                        },
                        {
                            "ID": 135,
                            "Name": "Canberra",
                            "CountryCode": "AUS",
                            "District": "Capital Region",
                            "Population": 322723
                        },
                        {
                            "ID": 136,
                            "Name": "Gold Coast",
                            "CountryCode": "AUS",
                            "District": "Queensland",
                            "Population": 311932
                        },
                        {
                            "ID": 137,
                            "Name": "Newcastle",
                            "CountryCode": "AUS",
                            "District": "New South Wales",
                            "Population": 270324
                        },
                        {
                            "ID": 138,
                            "Name": "Central Coast",
                            "CountryCode": "AUS",
                            "District": "New South Wales",
                            "Population": 227657
                        },
                        {
                            "ID": 139,
                            "Name": "Wollongong",
                            "CountryCode": "AUS",
                            "District": "New South Wales",
                            "Population": 219761
                        },
                        {
                            "ID": 140,
                            "Name": "Hobart",
                            "CountryCode": "AUS",
                            "District": "Tasmania",
                            "Population": 126118
                        },
                        {
                            "ID": 141,
                            "Name": "Geelong",
                            "CountryCode": "AUS",
                            "District": "Victoria",
                            "Population": 125382
                        },
                        {
                            "ID": 142,
                            "Name": "Townsville",
                            "CountryCode": "AUS",
                            "District": "Queensland",
                            "Population": 109914
                        },
                        {
                            "ID": 143,
                            "Name": "Cairns",
                            "CountryCode": "AUS",
                            "District": "Queensland",
                            "Population": 92273
                        }
                    ],
                    "languages": [
                        {
                            "CountryCode": "AUS",
                            "Language": "Arabic",
                            "IsOfficial": "F",
                            "Percentage": 1
                        },
                        {
                            "CountryCode": "AUS",
                            "Language": "Canton Chinese",
                            "IsOfficial": "F",
                            "Percentage": 1.1
                        },
                        {
                            "CountryCode": "AUS",
                            "Language": "English",
                            "IsOfficial": "T",
                            "Percentage": 81.2
                        },
                        {
                            "CountryCode": "AUS",
                            "Language": "German",
                            "IsOfficial": "F",
                            "Percentage": 0.6
                        },
                        {
                            "CountryCode": "AUS",
                            "Language": "Greek",
                            "IsOfficial": "F",
                            "Percentage": 1.6
                        },
                        {
                            "CountryCode": "AUS",
                            "Language": "Italian",
                            "IsOfficial": "F",
                            "Percentage": 2.2
                        },
                        {
                            "CountryCode": "AUS",
                            "Language": "Serbo-Croatian",
                            "IsOfficial": "F",
                            "Percentage": 0.6
                        },
                        {
                            "CountryCode": "AUS",
                            "Language": "Vietnamese",
                            "IsOfficial": "F",
                            "Percentage": 0.8
                        }
                    ]
                    }
                }
                ]
            }
        }';
    }
}
