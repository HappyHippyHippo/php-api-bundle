<?php

namespace Hippy\Api\Tests\Functional\Flow\Base;

use Hippy\Api\Tests\Functional\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class CheckEndpointTest extends EndpointTester
{
    /**
     * @return void
     * @coversNothing
     */
    public function testInvalidStringDeepValue(): void
    {
        $expected = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's-1.e2.p1.c460', 'message' => 'deep parameter must be a boolean']],
            ],
        ];

        $this->client->request('GET', '/__check?deep=__invalid_string_value__');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);
    }

    /**
     * @return void
     * @coversNothing
     */
    public function testInvalidArrayDeepValue(): void
    {
        $expected = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's-1.e2.p1.c460', 'message' => 'deep parameter must be a boolean']],
            ],
        ];

        $this->client->request('GET', '/__check?deep[]=1');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);
    }

    /**
     * @return void
     * @coversNothing
     */
    public function testCallShallowRequest(): void
    {
        $expected = [
            'status' => ['success' => true, 'errors' => []],
            'data' => ['checks' => []],
        ];

        $this->client->request('GET', '/__check?deep=false');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);
    }

    /**
     * @return void
     * @coversNothing
     */
    public function testCallDeepRequest(): void
    {
        $expected = [
            'status' => ['success' => true, 'errors' => []],
            'data' => ['checks' => []],
        ];

        $this->client->request('GET', '/__check?deep=true');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);
    }
}
