<?php

namespace Hippy\Api\Tests\Functional\Flow\Base;

use Hippy\Api\Tests\Functional\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class ConfigEndpointTest extends EndpointTester
{
    /**
     * @return void
     * @coversNothing
     */
    public function testCall(): void
    {
        putenv('HIPPY_ENDPOINT_CONFIG_ENABLED=1');
        $this->client->request('GET', '/__config');
        putenv('HIPPY_ENDPOINT_CONFIG_ENABLED=');

        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsArray($responseBody);
    }
}
