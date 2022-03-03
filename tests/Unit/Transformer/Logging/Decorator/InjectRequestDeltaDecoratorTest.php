<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Decorator;

use Hippy\Api\Transformer\Logging\Decorator\InjectRequestDeltaDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Decorator\InjectRequestDeltaDecorator */
class InjectRequestDeltaDecoratorTest extends TestCase
{
    /**
     * @return void
     * @covers ::response
     */
    public function testResponse(): void
    {
        $time = microtime(true);
        $data = ['request' => ['headers' => []], 'response' => ['headers' => []]];

        $request = $this->createMock(Request::class);
        $request->server = new ServerBag(['REQUEST_TIME_FLOAT', $time]);
        $response = $this->createMock(Response::class);

        $sut = new InjectRequestDeltaDecorator();
        $result = $sut->response($data, $request, $response);
        $this->assertArrayHasKey('delta', $result);
        $this->assertTrue($result['delta'] >= 0);
    }
}
