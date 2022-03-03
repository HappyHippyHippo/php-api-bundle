<?php

namespace Hippy\Api\Tests\Functional\Flow\Base;

use Hippy\Api\Tests\Functional\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class IndexEndpointTest extends EndpointTester
{
    /**
     * @return void
     * @coversNothing
     */
    public function testCall(): void
    {
        $expected = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'name' => 'unknown',
                'version' => 'development',
                'routes' => [
                    'base.check' => '[GET] /__check',
                    'base.check.preflight' => '[OPTIONS] /__check',
                    'base.config' => '[GET] /__config',
                    'base.config.preflight' => '[OPTIONS] /__config',
                    'base.index' => '[GET] /',
                    'base.index.preflight' => '[OPTIONS] /',
                    'base.openapi' => '[GET] /__openapi',
                    'base.openapi.preflight' => '[OPTIONS] /__openapi',
                ],
            ],
        ];

        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);
    }
}
