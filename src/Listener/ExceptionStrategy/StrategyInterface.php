<?php

namespace Hippy\Api\Listener\ExceptionStrategy;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

interface StrategyInterface
{
    /**
     * @param ExceptionEvent $event
     * @return bool
     */
    public function supports(ExceptionEvent $event): bool;

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function handle(ExceptionEvent $event): void;
}
