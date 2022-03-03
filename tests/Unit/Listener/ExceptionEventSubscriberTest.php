<?php

namespace Hippy\Api\Tests\Unit\Listener;

use Hippy\Api\Listener\ExceptionEventSubscriber;
use Hippy\Api\Listener\ExceptionStrategy\StrategyInterface;
use Hippy\Exception\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpKernel\KernelEvents;

/** @coversDefaultClass \Hippy\Api\Listener\ExceptionEventSubscriber */
class ExceptionEventSubscriberTest extends TestCase
{
    use EventCreatorTrait;

    /**
     * @return void
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals([
            KernelEvents::EXCEPTION => [['onKernelException', 1000]],
        ], ExceptionEventSubscriber::getSubscribedEvents());
    }

    /**
     * @param StrategyInterface[] $strategies
     * @param StrategyInterface[] $expected
     * @return void
     * @covers ::__construct
     * @dataProvider providerForConstructTests
     */
    public function testConstruct(array $strategies, array $expected): void
    {
        $sut = new ExceptionEventSubscriber($strategies);

        $property = new ReflectionProperty(ExceptionEventSubscriber::class, 'strategies');
        $this->assertSame($expected, $property->getValue($sut));
    }

    /**
     * @return array<string, array<string, array<int, StrategyInterface&MockObject>>>
     */
    public function providerForConstructTests(): array
    {
        /**
         * @param array $classes
         * @return array<string, StrategyInterface[]>
         */
        $creator = function (array $classes): array {
            $strategies = [];
            $expected = [];
            foreach ($classes as $class) {
                /** @var StrategyInterface&MockObject $mock */
                $mock = $this->createMock($class); // @phpstan-ignore-line
                $strategies[] = $mock;
                if ($mock instanceof StrategyInterface) {
                    $expected[] = $mock;
                }
            }

            return ['strategies' => $strategies, 'expected' => $expected];
        };

        return [
            'empty strategy list' => $creator([]),
            'all valid strategies list' => $creator([
                StrategyInterface::class,
                StrategyInterface::class,
                StrategyInterface::class,
                StrategyInterface::class,
            ]),
            'invalid strategy in list' => $creator([
                StrategyInterface::class,
                StrategyInterface::class,
                ExceptionEventSubscriber::class,
                StrategyInterface::class,
            ]),
        ];
    }

    /**
     * @return void
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionNoActionOnNoStrategy(): void
    {
        $event = $this->createExceptionEvent(new Exception());

        $sut = new ExceptionEventSubscriber([]);
        $sut->onKernelException($event);

        $this->assertNull($event->getResponse());
        $this->assertTrue($event->isPropagationStopped());
    }

    /**
     * @return void
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionNoActionOnNoStrategyFound(): void
    {
        $event = $this->createExceptionEvent(new Exception());

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($event)->willReturn(false);

        $sut = new ExceptionEventSubscriber([$strategy]);
        $sut->onKernelException($event);

        $this->assertNull($event->getResponse());
        $this->assertTrue($event->isPropagationStopped());
    }

    /**
     * @return void
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionStopIterationIfProcessed(): void
    {
        $event = $this->createExceptionEvent(new Exception());

        $strategies = [];

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($event)->willReturn(false);
        $strategies[] = $strategy;

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->once())->method('supports')->with($event)->willReturn(true);
        $strategy->expects($this->once())->method('handle')->with($event);
        $strategies[] = $strategy;

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->never())->method('supports');
        $strategies[] = $strategy;

        $sut = new ExceptionEventSubscriber($strategies);
        $sut->onKernelException($event);

        $this->assertTrue($event->isPropagationStopped());
    }
}
