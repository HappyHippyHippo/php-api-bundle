<?php

namespace Hippy\Api\Listener;

use Hippy\Api\Error\ErrorCode;
use Hippy\Error\Error;
use Hippy\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use UnexpectedValueException;

class JsonEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array<array<mixed>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 10]],
        ];
    }

    /**
     * @param RequestEvent $event
     * @return void
     * @throws Exception
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->headers->has('Content-Type')) {
            return;
        }

        $contentType = $request->headers->get('Content-Type');
        if (is_string($contentType) && str_contains($contentType, 'application/json')) {
            $post = json_decode((string) $request->getContent(), true);
            if (!is_array($post)) {
                throw (new Exception(Response::HTTP_BAD_REQUEST))->addError(
                    new Error(ErrorCode::MALFORMED_JSON, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::MALFORMED_JSON])
                );
            }

            $request->request->add($post);
        }
    }
}
