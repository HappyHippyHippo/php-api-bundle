<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Decorator;

use Hippy\Api\Transformer\Logging\Decorator\InjectResponseBodyDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Decorator\InjectResponseBodyDecorator */
class InjectResponseBodyDecoratorTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getExpectedStateCode
     */
    public function testGetExpectedStateCode(): void
    {
        $sut = new InjectResponseBodyDecorator();

        $this->assertEquals(Response::HTTP_OK, $sut->getExpectedStateCode());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::setExpectedStateCode
     */
    public function testSetExpectedStateCode(): void
    {
        $sut = new InjectResponseBodyDecorator();

        $this->assertEquals(Response::HTTP_OK, $sut->getExpectedStateCode());
        $sut->setExpectedStateCode(Response::HTTP_NO_CONTENT);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $sut->getExpectedStateCode());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::response
     */
    public function testResponseApplyIfStatusCodeIsNotTheExpectedOne(): void
    {
        $statusCode = 123;
        $body = ['field' => '__dummy_body__'];
        $data = ['request' => ['headers' => []], 'response' => ['headers' => []]];
        $expected = ['request' => ['headers' => []], 'response' => ['headers' => [], 'body' => $body]];

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);
        $response->expects($this->once())->method('getContent')->willReturn(json_encode($body));

        $sut = new InjectResponseBodyDecorator();
        $this->assertEquals($expected, $sut->response($data, $request, $response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::response
     */
    public function testResponseNoOpIfStatusCodeIsTheExpectedOne(): void
    {
        $statusCode = 123;
        $data = ['request' => ['headers' => []], 'response' => ['headers' => []]];
        $expected = ['request' => ['headers' => []], 'response' => ['headers' => []]];

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);
        $response->expects($this->never())->method('getContent');

        $sut = new InjectResponseBodyDecorator($statusCode);
        $this->assertEquals($expected, $sut->response($data, $request, $response));
    }
}
