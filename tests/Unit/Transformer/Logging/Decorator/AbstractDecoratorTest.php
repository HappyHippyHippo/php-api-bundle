<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Decorator;

use Hippy\Api\Transformer\Logging\Decorator\AbstractDecorator;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Decorator\AbstractDecorator */
class AbstractDecoratorTest extends TestCase
{
    /**
     * return void
     * @covers ::request
     * @covers ::response
     * @covers ::exception
     */
    public function testCalls(): void
    {
        $data = ['__dummy_field__' => '__dummy_data__'];

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $exception = new Exception();

        $sut = $this->getMockForAbstractClass(AbstractDecorator::class);
        $this->assertEquals($data, $sut->request($data, $request));
        $this->assertEquals($data, $sut->response($data, $request, $response));
        $this->assertEquals($data, $sut->exception($data, $request, $exception));
    }
}
