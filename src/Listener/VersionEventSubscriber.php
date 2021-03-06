<?php

namespace Hippy\Api\Listener;

use Hippy\Api\Config\ApiConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VersionEventSubscriber implements EventSubscriberInterface
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
        if ($this->config->isHeaderVersionEnabled()) {
            $response = $event->getResponse();
            $response->headers->add(['X-API-Version' => $this->config->getAppVersion()]);
        }
    }
}
