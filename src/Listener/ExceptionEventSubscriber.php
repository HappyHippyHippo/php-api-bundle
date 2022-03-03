<?php

namespace Hippy\Api\Listener;

use Hippy\Api\Listener\ExceptionStrategy\StrategyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array<array<mixed>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [['onKernelException', 1000]],
        ];
    }

    /** @var StrategyInterface[] */
    protected array $strategies;

    /**
     * @param StrategyInterface[] $strategies
     */
    public function __construct(iterable $strategies = [])
    {
        $this->strategies = [];
        foreach ($strategies as $strategy) {
            if ($strategy instanceof StrategyInterface) {
                $this->strategies[] = $strategy;
            }
        }
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($event)) {
                $strategy->handle($event);

                break;
            }
        }
        $event->stopPropagation();
    }
}
