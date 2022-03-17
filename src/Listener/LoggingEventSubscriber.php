<?php

namespace Hippy\Api\Listener;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Transformer\Logging\TransformerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LoggingEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array<array<mixed>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 0]],
            KernelEvents::RESPONSE => [['onKernelResponse', 0]],
            KernelEvents::EXCEPTION => [['onKernelException', 0]],
        ];
    }

    /**
     * @param ApiConfig $config
     * @param TransformerInterface $transformer
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ApiConfig $config,
        protected TransformerInterface $transformer,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->config->isLogRequestEnabled()) {
            $level = $this->config->getLogRequestLevel();
            $this->logger->$level(
                $this->config->getLogRequestMessage(),
                $this->transformer->request($event->getRequest())
            );
        }
    }

    /**
     * @param ResponseEvent $event
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->config->isLogResponseEnabled()) {
            $level = $this->config->getLogResponseLevel();
            $this->logger->$level(
                $this->config->getLogResponseMessage(),
                $this->transformer->response($event->getRequest(), $event->getResponse())
            );
        }
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->config->isLogExceptionEnabled()) {
            $level = $this->config->getLogExceptionLevel();
            $this->logger->$level(
                $this->config->getLogExceptionMessage(),
                $this->transformer->exception($event->getRequest(), $event->getThrowable())
            );
        }
    }
}
