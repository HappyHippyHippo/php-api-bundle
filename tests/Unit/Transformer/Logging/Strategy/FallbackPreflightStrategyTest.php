<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Strategy;

use Hippy\Api\Transformer\Logging\Decorator\HeaderCleanerDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectRequestDeltaDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectResponseBodyDecorator;
use Hippy\Api\Transformer\Logging\Strategy\FallbackPreflightStrategy;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Strategy\FallbackPreflightStrategy */
class FallbackPreflightStrategyTest extends TestCase
{
    /** @var FallbackPreflightStrategy */
    private FallbackPreflightStrategy $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->sut = new FallbackPreflightStrategy();
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $prop = new ReflectionProperty(FallbackPreflightStrategy::class, 'decorators');
        $decorators = $prop->getValue($this->sut);

        $this->assertInstanceOf(HeaderCleanerDecorator::class, $decorators[0]);
        $this->assertInstanceOf(InjectRequestDeltaDecorator::class, $decorators[1]);
        $this->assertInstanceOf(InjectResponseBodyDecorator::class, $decorators[2]);

        $prop = new ReflectionProperty(InjectResponseBodyDecorator::class, 'expectedStatusCode');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $prop->getValue($decorators[2]));
    }

    /**
     * @return void
     * @covers ::priority
     */
    public function testPriority(): void
    {
        $this->assertEquals(-5, $this->sut->priority());
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupportsFailIfRouteDoesntExists(): void
    {
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => '']);

        $this->assertFalse($this->sut->supports($request));
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupportsFailIfRouteDontEndsWithPreflight(): void
    {
        $route = '__dummy_route_name__';

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => $route]);

        $this->assertFalse($this->sut->supports($request));
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupportsSucceedIfRouteEndsWithPreflight(): void
    {
        $route = '__dummy_route_name__' . '.preflight';

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => $route]);

        $this->assertTrue($this->sut->supports($request));
    }
}
