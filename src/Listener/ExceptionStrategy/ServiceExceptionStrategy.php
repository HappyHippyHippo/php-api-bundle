<?php

namespace Hippy\Api\Listener\ExceptionStrategy;

use Hippy\Exception\Exception;
use Hippy\Model\Envelope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ServiceExceptionStrategy implements StrategyInterface
{
    /**
     * @param ExceptionEvent $event
     * @return bool
     */
    public function supports(ExceptionEvent $event): bool
    {
        return $event->getThrowable() instanceof Exception;
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function handle(ExceptionEvent $event): void
    {
        /** @var Exception $exception */
        $exception = $event->getThrowable();

        $envelope = (new Envelope($exception->getErrors()));
        $data = $exception->getData();
        if (!is_null($data)) {
            $envelope->setData($data);
        }
        $event->setResponse(new JsonResponse($envelope, $exception->getStatusCode()));
    }
}
