<?php

namespace Hippy\Api\Tests\Unit\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

trait EventCreatorTrait
{
    /**
     * @param Request|null $request
     * @return RequestEvent
     */
    protected function createRequestEvent(?Request $request = null): RequestEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $request ?? $this->createMock(Request::class);
        $requestType = 0;

        return new RequestEvent($kernel, $request, $requestType);
    }

    /**
     * @param Request|null $request
     * @param Response|null $response
     * @return ResponseEvent
     */
    protected function createResponseEvent(?Request $request = null, ?Response $response = null): ResponseEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $request ?? $this->createMock(Request::class);
        $response = $response ?? $this->createMock(Response::class);
        $requestType = 0;

        return new ResponseEvent($kernel, $request, $requestType, $response);
    }

    /**
     * @param Throwable|null $exception
     * @param Request|null $request
     * @return ExceptionEvent
     */
    protected function createExceptionEvent(?Throwable $exception = null, ?Request $request = null): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $request ?? $this->createMock(Request::class);
        $exception = $exception ?? $this->createMock(Throwable::class);
        $requestType = 0;

        return new ExceptionEvent($kernel, $request, $requestType, $exception);
    }
}
