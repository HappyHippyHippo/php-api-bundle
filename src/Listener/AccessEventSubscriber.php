<?php

namespace Hippy\Api\Listener;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Error\ErrorCode;
use Hippy\Error\Error;
use Hippy\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AccessEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array<array<mixed>>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [['onKernelController', 10]],
        ];
    }

    /**
     * @param ApiConfig $config
     */
    public function __construct(protected ApiConfig $config)
    {
    }

    /**
     * @param ControllerEvent $event
     * @return void
     * @throws Exception
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        $route = $request->attributes->get('_route');
        if (!is_string($route)) {
            throw (new Exception(Response::HTTP_INTERNAL_SERVER_ERROR))->addError(
                new Error(ErrorCode::UNKNOWN_ROUTE, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::UNKNOWN_ROUTE])
            );
        }
        $route = str_replace('-', '_', $route);

        $remoteAddress = $request->server->get('REMOTE_ADDR');
        $remoteHost = $request->server->get('REMOTE_HOST');

        $sourcePool = [
            is_string($remoteAddress) ? $remoteAddress : '',
            is_string($remoteHost) ? $remoteHost : '',
            $request->headers->get('origin') ?? '',
            $request->headers->get('referer') ?? '',
        ];

        $allowed = $this->allowGlobals($sourcePool)
            && $this->allowEndpoint($route, $sourcePool)
            && !$this->denyGlobals($sourcePool)
            && !$this->denyEndpoint($route, $sourcePool);

        if (!$allowed) {
            throw (new Exception(Response::HTTP_FORBIDDEN))->addError(
                new Error(ErrorCode::NOT_ALLOWED, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::NOT_ALLOWED])
            );
        }
    }

    /**
     * @param string[] $sourcePool
     * @return bool
     */
    protected function allowGlobals(array $sourcePool): bool
    {
        $allowed = $this->config->getAccessAllowGlobals();
        if (!count($allowed)) {
            return true;
        }

        return count(array_intersect($allowed, $sourcePool)) > 0;
    }

    /**
     * @param string $route
     * @param string[] $sourcePool
     * @return bool
     */
    protected function allowEndpoint(string $route, array $sourcePool): bool
    {
        $allowed = $this->config->getAccessAllowEndpoints();
        if (!count($allowed) || !isset($allowed[$route])) {
            return true;
        }

        return count(array_intersect($allowed[$route], $sourcePool)) > 0;
    }

    /**
     * @param string[] $sourcePool
     * @return bool
     */
    protected function denyGlobals(array $sourcePool): bool
    {
        $blocked = $this->config->getAccessDenyGlobals();
        if (!count($blocked)) {
            return false;
        }

        return count(array_intersect($blocked, $sourcePool)) > 0;
    }

    /**
     * @param string $route
     * @param string[] $sourcePool
     * @return bool
     */
    protected function denyEndpoint(string $route, array $sourcePool): bool
    {
        $blocked = $this->config->getAccessDenyEndpoints();
        if (!count($blocked) || !isset($blocked[$route])) {
            return false;
        }

        return count(array_intersect($blocked[$route], $sourcePool)) > 0;
    }
}
