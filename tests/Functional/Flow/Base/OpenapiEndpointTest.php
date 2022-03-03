<?php

namespace Hippy\Api\Tests\Functional\Flow\Base;

use Hippy\Api\Tests\Functional\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class OpenapiEndpointTest extends EndpointTester
{
    /**
     * @return void
     * @coversNothing
     */
    public function testCall(): void
    {
        putenv('HIPPY_ENDPOINT_OPENAPI_ENABLED=1');
        $this->client->request('GET', '/__openapi');
        putenv('HIPPY_ENDPOINT_OPENAPI_ENABLED=');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Content-Type'));
        $this->assertEquals('text/vnd.yaml; charset=UTF-8', $response->headers->get('Content-Type'));
    }
}
