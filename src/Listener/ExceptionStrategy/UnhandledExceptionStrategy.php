<?php

namespace Hippy\Api\Listener\ExceptionStrategy;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Hippy\Error\ExceptionError;
use Hippy\Model\Envelope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;

class UnhandledExceptionStrategy implements StrategyInterface
{
    /**
     * @param ApiConfigInterface $config
     */
    public function __construct(protected ApiConfigInterface $config)
    {
    }

    /**
     * @param ExceptionEvent $event
     * @return bool
     */
    public function supports(ExceptionEvent $event): bool
    {
        return true;
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function handle(ExceptionEvent $event): void
    {
        $collection = new ErrorCollection();
        $collection->add($this->createError($event->getThrowable()));
        $envelope = new Envelope($collection);

        $event->setResponse(new JsonResponse($envelope, Response::HTTP_INTERNAL_SERVER_ERROR));
    }

    /**
     * @param Throwable $exception
     * @return Error
     */
    private function createError(Throwable $exception): Error
    {
        if (!$this->config->isErrorTraceEnabled()) {
            return new Error($exception->getCode(), $exception->getMessage());
        }

        return new ExceptionError($exception->getCode(), $exception->getMessage(), $exception);
    }
}
