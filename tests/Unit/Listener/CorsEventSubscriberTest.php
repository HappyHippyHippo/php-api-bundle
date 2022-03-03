<?php

namespace Hippy\Api\Tests\Unit\Listener;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Listener\CorsEventSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/** @coversDefaultClass \Hippy\Api\Listener\CorsEventSubscriber */
class CorsEventSubscriberTest extends TestCase
{
    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var CorsEventSubscriber  */
    private CorsEventSubscriber $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->sut = new CorsEventSubscriber($this->config);
    }

    /**
     * @return void
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals([
            KernelEvents::RESPONSE => [['onKernelResponse', 0]],
        ], CorsEventSubscriber::getSubscribedEvents());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelResponse
     */
    public function testOnKernelResponseEventOnDisabled(): void
    {
        $this->config->expects($this->once())->method('isCorsEnabled')->willReturn(false);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response->headers = new ResponseHeaderBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->sut->onKernelResponse(new ResponseEvent($kernel, $request, $requestType, $response));

        $this->assertArrayNotHasKey('access-control-allow-origin', $response->headers->all());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelResponse
     */
    public function testOnKernelResponseEvent(): void
    {
        $origin = '__dummy_origin__';

        $this->config->expects($this->once())->method('isCorsEnabled')->willReturn(true);
        $this->config->expects($this->once())->method('getCorsOrigin')->willReturn($origin);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response->headers = new ResponseHeaderBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->sut->onKernelResponse(new ResponseEvent($kernel, $request, $requestType, $response));

        $this->assertArrayHasKey('access-control-allow-origin', $response->headers->all());
    }
}
