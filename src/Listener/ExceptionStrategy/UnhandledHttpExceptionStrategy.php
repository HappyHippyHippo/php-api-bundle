<?php

namespace Hippy\Api\Listener\ExceptionStrategy;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Hippy\Error\ExceptionError;
use Hippy\Model\Envelope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class UnhandledHttpExceptionStrategy implements StrategyInterface
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
        return $event->getThrowable() instanceof HttpException;
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function handle(ExceptionEvent $event): void
    {
        /** @var HttpException $exception */
        $exception = $event->getThrowable();

        $collection = new ErrorCollection();
        $collection->add($this->createError($exception));
        $envelope = new Envelope($collection);

        $event->setResponse(new JsonResponse($envelope, $exception->getStatusCode()));
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
