<?php

namespace Hippy\Api\Tests\Unit\Model\Controller;

use Hippy\Api\Model\Controller\AuthRequestModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/** @coversDefaultClass \Hippy\Api\Model\Controller\AuthRequestModel */
class AuthRequestModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaderAuthTokenId
     * @covers ::getHeaderAuthUserId
     * @covers ::getHeaderAuthUserUuid
     * @covers ::getHeaderAuthUserEmail
     */
    public function testConstruct(): void
    {
        $request = new Request();
        $model = new AuthRequestModel($request);

        $this->assertEquals([], $model->jsonSerialize());
        $this->assertEquals('', $model->getHeaderRequestId());
        $this->assertEquals('', $model->getHeaderAuthTokenId());
        $this->assertEquals(0, $model->getHeaderAuthUserId());
        $this->assertEquals('', $model->getHeaderAuthUserUuid());
        $this->assertEquals('', $model->getHeaderAuthUserEmail());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getRequest
     */
    public function testGetRequest(): void
    {
        $request = new Request();
        $model = new AuthRequestModel($request);

        $this->assertSame($request, $model->getRequest());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaderAuthTokenId
     */
    public function testGetHeaderAuthTokenId(): void
    {
        $header = '__dummy_value__';
        $request = new Request();
        $request->headers->set('X-Auth-Token-ID', $header);
        $model = new AuthRequestModel($request);

        $this->assertSame($header, $model->getHeaderAuthTokenId());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaderAuthUserId
     */
    public function testGetHeaderAuthUserId(): void
    {
        $header = '123';
        $request = new Request();
        $request->headers->set('X-Auth-User-ID', $header);
        $model = new AuthRequestModel($request);

        $this->assertSame(123, $model->getHeaderAuthUserId());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaderAuthUserUuid
     */
    public function testGetHeaderAuthUserUuid(): void
    {
        $header = '__dummy_value__';
        $request = new Request();
        $request->headers->set('X-Auth-User-UUID', $header);
        $model = new AuthRequestModel($request);

        $this->assertSame($header, $model->getHeaderAuthUserUuid());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaderAuthUserEmail
     */
    public function testGetHeaderAuthUserEmail(): void
    {
        $header = '__dummy_value__';
        $request = new Request();
        $request->headers->set('X-Auth-User-Email', $header);
        $model = new AuthRequestModel($request);

        $this->assertSame($header, $model->getHeaderAuthUserEmail());
    }
}
