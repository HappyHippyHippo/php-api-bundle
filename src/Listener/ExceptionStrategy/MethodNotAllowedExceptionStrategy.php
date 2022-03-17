<?php

namespace Hippy\Api\Listener\ExceptionStrategy;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Error\ErrorCode;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Hippy\Error\ExceptionError;
use Hippy\Model\Envelope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class MethodNotAllowedExceptionStrategy implements StrategyInterface
{
    /**
     * @param ApiConfig $config
     */
    public function __construct(protected ApiConfig $config)
    {
    }

    /**
     * @param ExceptionEvent $event
     * @return bool
     */
    public function supports(ExceptionEvent $event): bool
    {
        return $event->getThrowable() instanceof MethodNotAllowedException;
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $collection = new ErrorCollection();
        $collection->add($this->createError($exception));
        $envelope = new Envelope($collection);

        $event->setResponse(new JsonResponse($envelope, Response::HTTP_METHOD_NOT_ALLOWED));
    }

    /**
     * @param Throwable $exception
     * @return Error
     */
    private function createError(Throwable $exception): Error
    {
        if (!$this->config->isErrorTraceEnabled()) {
            return new Error(ErrorCode::NOT_ALLOWED, $exception->getMessage());
        }

        return new ExceptionError(ErrorCode::NOT_ALLOWED, $exception->getMessage(), $exception);
    }
}
