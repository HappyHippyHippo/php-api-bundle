<?php

namespace Hippy\Api\Tests\Unit\Model\Controller;

use Hippy\Api\Model\Controller\RequestModel;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\ParameterBag;
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
     */
    public function testSearchHeaderIgnoreEmptyHeaderObject(): void
    {
        $request = new Request();
        $model = new RequestModel($request);

        $this->assertNull($model->getHeaderRequestId());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBagBool
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagBoolReturnCorrectlyConvertedValue(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = 'true';
        $bag = $this->createMock(ParameterBag::class);
        $bag->expects($this->once())->method('get')->with($field, $default)->willReturn($value);

        $method = new ReflectionMethod(RequestModel::class, 'searchBagBool');
        $this->assertTrue($method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBagBool
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagBoolReturnInvalidValueIfNotAbleToConvert(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = '__dummy_value__';
        $bag = $this->createMock(ParameterBag::class);
        $bag->expects($this->once())->method('get')->with($field, $default)->willReturn($value);

        $method = new ReflectionMethod(RequestModel::class, 'searchBagBool');
        $this->assertEquals($value, $method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBagInt
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagIntReturnCorrectlyConvertedValue(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = 123;
        $bag = $this->createMock(ParameterBag::class);
        $bag->expects($this->once())->method('get')->with($field, $default)->willReturn($value . '');

        $method = new ReflectionMethod(RequestModel::class, 'searchBagInt');
        $this->assertEquals($value, $method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBagInt
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagIntReturnInvalidValueIfNotAbleToConvert(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = '__dummy_value__';
        $bag = $this->createMock(ParameterBag::class);
        $bag->expects($this->once())->method('get')->with($field, $default)->willReturn($value);

        $method = new ReflectionMethod(RequestModel::class, 'searchBagInt');
        $this->assertEquals($value, $method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBagFloat
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagFloatReturnCorrectlyConvertedValue(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = 123.456;
        $bag = $this->createMock(ParameterBag::class);
        $bag->expects($this->once())->method('get')->with($field, $default)->willReturn($value . '');

        $method = new ReflectionMethod(RequestModel::class, 'searchBagFloat');
        $this->assertEquals($value, $method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBagFloat
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagFloatReturnInvalidValueIfNotAbleToConvert(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = '__dummy_value__';
        $bag = $this->createMock(ParameterBag::class);
        $bag->expects($this->once())->method('get')->with($field, $default)->willReturn($value);

        $method = new ReflectionMethod(RequestModel::class, 'searchBagFloat');
        $this->assertEquals($value, $method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagReturnFoundValue(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = '__dummy_value__';
        $bag = $this->createMock(ParameterBag::class);
        $bag->expects($this->once())->method('get')->with($field, $default)->willReturn($value);

        $method = new ReflectionMethod(RequestModel::class, 'searchBag');
        $this->assertEquals($value, $method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::searchBag
     * @throws ReflectionException
     */
    public function testSearchBagReturnAllCallOnGetException(): void
    {
        $field = '__dummy_field__';
        $default = '__dummy_default__';
        $value = ['__dummy_value__'];
        $bag = $this->createMock(ParameterBag::class);
        $bag
            ->expects($this->once())
            ->method('get')
            ->with($field, $default)
            ->willThrowException(new BadRequestException());
        $bag
            ->expects($this->once())
            ->method('all')
            ->with($field)
            ->willReturn($value);

        $method = new ReflectionMethod(RequestModel::class, 'searchBag');
        $this->assertEquals($value, $method->invoke(new RequestModel(new Request()), $bag, $field, $default));
    }
}
