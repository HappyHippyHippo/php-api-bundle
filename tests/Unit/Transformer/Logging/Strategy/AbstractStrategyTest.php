<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Strategy;

use Hippy\Api\Transformer\Logging\Decorator\DecoratorInterface;
use Hippy\Api\Transformer\Logging\Strategy\AbstractStrategy;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Strategy\AbstractStrategy */
class AbstractStrategyTest extends TestCase
{
    /** @var string */
    protected string $acceptedRoute;

    /** @var DecoratorInterface&MockObject */
    protected DecoratorInterface $decorator;

    /** @var int */
    protected int $priority;

    /** @var AbstractStrategy&MockObject */
    protected AbstractStrategy $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->acceptedRoute = '__accepted_route__';
        $this->decorator = $this->createMock(DecoratorInterface::class);
        $this->priority = 123;

        $this->sut = $this->getMockForAbstractClass(
            AbstractStrategy::class,
            [
                $this->acceptedRoute,
                [$this->decorator],
                $this->priority
            ],
        );
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::priority
     */
    public function testPriority(): void
    {
        $this->assertEquals($this->priority, $this->sut->priority());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::supports
     */
    public function testSupportsReturnFalseOnNonSupportedRoute(): void
    {
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => 'unsupported']);

        $this->assertFalse($this->sut->supports($request));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::supports
     */
    public function testSupportsReturnTrueOnSupportedRoute(): void
    {
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => $this->acceptedRoute]);

        $this->assertTrue($this->sut->supports($request));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::request
     */
    public function testRequest(): void
    {
        $data = ['__dummy_section__' => ['__dummy_field__' => '__dummy_data__']];
        $decorated = ['__dummy_decorated_data__'];

        $request = $this->createMock(Request::class);

        $this->decorator
            ->expects($this->once())
            ->method('request')
            ->with($data, $request)
            ->willReturn($decorated);

        $this->assertEquals($decorated, $this->sut->request($data, $request));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::response
     */
    public function testResponse(): void
    {
        $data = ['__dummy_section__' => ['__dummy_field__' => '__dummy_data__']];
        $decorated = ['__dummy_decorated_data__'];

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $this->decorator
            ->expects($this->once())
            ->method('response')
            ->with($data, $request, $response)
            ->willReturn($decorated);

        $this->assertEquals($decorated, $this->sut->response($data, $request, $response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::exception
     */
    public function testException(): void
    {
        $data = ['__dummy_section__' => ['__dummy_field__' => '__dummy_data__']];
        $decorated = ['__dummy_decorated_data__'];

        $request = $this->createMock(Request::class);
        $exception = new Exception('__dummy_message__');

        $this->decorator
            ->expects($this->once())
            ->method('exception')
            ->with($data, $request, $exception)
            ->willReturn($decorated);

        $this->assertEquals($decorated, $this->sut->exception($data, $request, $exception));
    }
}
