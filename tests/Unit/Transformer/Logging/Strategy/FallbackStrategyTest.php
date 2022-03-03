<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Strategy;

use Hippy\Api\Transformer\Logging\Decorator\HeaderCleanerDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectRequestDeltaDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectResponseBodyDecorator;
use Hippy\Api\Transformer\Logging\Strategy\FallbackStrategy;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Strategy\FallbackStrategy */
class FallbackStrategyTest extends TestCase
{
    /** @var FallbackStrategy */
    private FallbackStrategy $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->sut = new FallbackStrategy();
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $prop = new ReflectionProperty(FallbackStrategy::class, 'decorators');
        $decorators = $prop->getValue($this->sut);

        $this->assertInstanceOf(HeaderCleanerDecorator::class, $decorators[0]);
        $this->assertInstanceOf(InjectRequestDeltaDecorator::class, $decorators[1]);
        $this->assertInstanceOf(InjectResponseBodyDecorator::class, $decorators[2]);

        $prop = new ReflectionProperty(InjectResponseBodyDecorator::class, 'expectedStatusCode');
        $this->assertEquals(Response::HTTP_OK, $prop->getValue($decorators[2]));
    }

    /**
     * @return void
     * @covers ::priority
     */
    public function testPriority(): void
    {
        $this->assertEquals(-10, $this->sut->priority());
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupports(): void
    {
        $request = $this->createMock(Request::class);

        $this->assertTrue($this->sut->supports($request));
    }
}
