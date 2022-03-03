<?php

namespace Hippy\Api\Tests\Functional\Logging;

use Hippy\Api\Controller\Base\OpenapiController;
use Monolog\Handler\TestHandler;

class OpenApiEndpointTest extends EndpointTester
{
    /**
     * @return void
     * @coversNothing
     */
    public function testEndpoint(): void
    {
        putenv('HIPPY_ENDPOINT_OPENAPI_ENABLED=1');
        $this->client->request('GET', '/__openapi');
        putenv('HIPPY_ENDPOINT_OPENAPI_ENABLED=');

        $logger = static::getContainer()->get('monolog.handler.main');
        if (!($logger instanceof TestHandler)) {
            $this->fail('unable to retrieve the container logger');
        }

        $records = $logger->getRecords();
        $this->assertCount(2, $records);

        $record = $records[0];
        $this->checkRequest($record);
        $this->checkRequestAttr($record, '_route', 'base.openapi');

        $record = $records[1];
        $this->checkResponse($record);
        $this->checkRequestAttr($record, '_route', 'base.openapi');
        $this->checkResponseStatus($record, 200);
        $this->checkResponseNoBody($record);
    }
}
