<?php

namespace Hippy\Api\Tests\Unit\Model\Controller;

use Hippy\Api\Model\Controller\RequestModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/** @coversDefaultClass \Hippy\Api\Model\Controller\RequestModel */
class RequestModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $request = new Request();
        $model = new RequestModel($request);

        $this->assertEquals([], $model->jsonSerialize());
        $this->assertEquals(null, $model->getHeaderRequestId());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getRequest
     */
    public function testGetRequest(): void
    {
        $request = new Request();
        $model = new RequestModel($request);

        $this->assertSame($request, $model->getRequest());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchHeader
     * @covers ::getHeaderRequestId
     */
    public function testGetHeaderRequestId(): void
    {
        $requestId = '__dummy_request_id__';
        $request = new Request();
        $request->headers->set('x-ReQuEst-iD', $requestId);
        $model = new RequestModel($request);

        $this->assertSame($requestId, $model->getHeaderRequestId());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchHeader
     * @covers ::getHeaderRequestId
     */
    public function testSearchHeaderIgnoreNullHeaderObject(): void
    {
        $request = $this->createMock(Request::class);
        $model = new RequestModel($request);

        $this->assertNull($model->getHeaderRequestId());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchHeader
     * @covers ::getHeaderRequestId
     */
    public function testSearchHeaderIgnoreEmptyHeaderObject(): void
    {
        $request = new Request();
        $model = new RequestModel($request);

        $this->assertNull($model->getHeaderRequestId());
    }
}
