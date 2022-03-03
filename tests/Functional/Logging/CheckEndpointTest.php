<?php

namespace Hippy\Api\Tests\Functional\Logging;

use Monolog\Handler\TestHandler;

class CheckEndpointTest extends EndpointTester
{
    /**
     * @return void
     * @coversNothing
     */
    public function testEndpoint(): void
    {
        $this->client->request('GET', '/__check');

        $logger = static::getContainer()->get('monolog.handler.main');
        if (!($logger instanceof TestHandler)) {
            $this->fail('unable to retrieve the container logger');
        }

        $records = $logger->getRecords();
        $this->assertCount(2, $records);

        $record = $records[0];
        $this->checkRequest($record);
        $this->checkRequestAttr($record, '_route', 'base.check');

        $record = $records[1];
        $this->checkResponse($record);
        $this->checkRequestAttr($record, '_route', 'base.check');
        $this->checkResponseStatus($record, 200);
        $this->checkResponseNoBody($record);
    }
}
