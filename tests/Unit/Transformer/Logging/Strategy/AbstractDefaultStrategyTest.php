<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Strategy;

use Hippy\Api\Transformer\Logging\Decorator\DecoratorInterface;
use Hippy\Api\Transformer\Logging\Decorator\HeaderCleanerDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectRequestDeltaDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectResponseBodyDecorator;
use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy */
class AbstractDefaultStrategyTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $route = '__dummy_route__';
        $statusCode = 123;
        $priority = 456;

        $sut = $this->getMockForAbstractClass(AbstractDefaultStrategy::class, [$route, $statusCode, $priority]);

        $prop = new ReflectionProperty(AbstractDefaultStrategy::class, 'acceptedRoute');
        $this->assertEquals($route, $prop->getValue($sut));

        $prop = new ReflectionProperty(AbstractDefaultStrategy::class, 'decorators');
        /** @var DecoratorInterface[] $decorators */
        $decorators = $prop->getValue($sut);

        $searchable = [
            HeaderCleanerDecorator::class,
            InjectRequestDeltaDecorator::class,
            InjectResponseBodyDecorator::class,
        ];

        foreach ($searchable as $searching) {
            $this->assertTrue(
                array_reduce(
                    $decorators,
                    function (bool $carry, DecoratorInterface $decorator) use ($searching) {
                        return $carry || $decorator instanceof $searching;
                    },
                    false
                )
            );
        }

        $prop = new ReflectionProperty(InjectResponseBodyDecorator::class, 'expectedStatusCode');
        $this->assertEquals($statusCode, $prop->getValue($decorators[2]));

        $this->assertEquals($priority, $sut->priority());
    }
}
