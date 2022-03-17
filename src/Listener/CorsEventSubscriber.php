<?php

namespace Hippy\Api\Listener;

use Hippy\Api\Config\ApiConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array<array<mixed>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [['onKernelResponse', 0]],
        ];
    }

    /**
     * @param ApiConfig $config
     */
    public function __construct(protected ApiConfig $config)
    {
    }

    /**
     * @param ResponseEvent $event
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->config->isCorsEnabled()) {
            $response = $event->getResponse();
            $response->headers->add(['Access-Control-Allow-Origin' => $this->config->getCorsOrigin()]);
        }
    }
}
