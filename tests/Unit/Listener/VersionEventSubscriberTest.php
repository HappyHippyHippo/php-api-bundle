<?php

namespace Hippy\Api\Tests\Unit\Listener;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Listener\VersionEventSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/** @coversDefaultClass \Hippy\Api\Listener\VersionEventSubscriber */
class VersionEventSubscriberTest extends TestCase
{
    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var VersionEventSubscriber */
    private VersionEventSubscriber $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->sut = new VersionEventSubscriber($this->config);
    }

    /**
     * @return void
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals([
            KernelEvents::RESPONSE => [['onKernelResponse', 0]],
        ], VersionEventSubscriber::getSubscribedEvents());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelResponse
     */
    public function testOnKernelResponseEventIfDisabled(): void
    {
        $this->config->expects($this->once())->method('isHeaderVersionEnabled')->willReturn(false);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response->headers = new ResponseHeaderBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->sut->onKernelResponse(new ResponseEvent($kernel, $request, $requestType, $response));

        $this->assertArrayNotHasKey('x-api-version', $response->headers->all());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelResponse
     */
    public function testOnKernelResponseEvent(): void
    {
        $version = '__dummy_version__';

        $this->config->expects($this->once())->method('isHeaderVersionEnabled')->willReturn(true);
        $this->config->expects($this->once())->method('getAppVersion')->willReturn($version);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $response->headers = new ResponseHeaderBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->sut->onKernelResponse(new ResponseEvent($kernel, $request, $requestType, $response));

        $this->assertArrayHasKey('x-api-version', $response->headers->all());
        $this->assertEquals($version, $response->headers->get('x-api-version'));
    }
}
