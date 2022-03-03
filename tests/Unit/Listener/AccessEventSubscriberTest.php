<?php

namespace Hippy\Api\Tests\Unit\Listener;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Error\ErrorCode;
use Hippy\Api\Listener\AccessEventSubscriber;
use Hippy\Error\Error;
use Hippy\Exception\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @coversDefaultClass \Hippy\Api\Listener\AccessEventSubscriber
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class AccessEventSubscriberTest extends TestCase
{
    /** @var ApiConfigInterface&MockObject */
    protected ApiConfigInterface $config;

    /** @var Request&MockObject */
    protected Request $request;

    /** @var ControllerEvent */
    protected ControllerEvent $event;

    /** @var AccessEventSubscriber */
    protected AccessEventSubscriber $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfigInterface::class);
        $this->request = $this->createMock(Request::class);
        $this->sut = new AccessEventSubscriber($this->config);
    }

    /**
     * @return void
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals([
            KernelEvents::CONTROLLER => [['onKernelController', 10]],
        ], AccessEventSubscriber::getSubscribedEvents());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testNoList(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testMissesOnEndpointLists(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = ['__dummy_other_route__' => [$clientIp, $clientHost]];
        $denyList = ['__dummy_other_route__' => [$clientIp, $clientHost]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn($denyList);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testNotOnGlobalAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = ['__dummy_another__'];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn($allowList);
        $this->config->expects($this->never())->method('getAccessAllowEndpoints');
        $this->config->expects($this->never())->method('getAccessDenyGlobals');
        $this->config->expects($this->never())->method('getAccessDenyEndpoints');

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testIpOnGlobalAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = [$clientIp];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testHostOnGlobalAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = [$clientHost];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testOriginOnGlobalAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = [$clientOrigin];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testRefererOnGlobalAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = [$clientReferer];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testNotOnEndpointAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = ['__dummy_route__' => ['__dummy_another__']];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn($allowList);
        $this->config->expects($this->never())->method('getAccessDenyGlobals');
        $this->config->expects($this->never())->method('getAccessDenyEndpoints');

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testIpOnEndpointAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = ['__dummy_another_route__' => [], '__dummy_route__' => [$clientIp]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testHostOnEndpointAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = ['__dummy_another_route__' => [], '__dummy_route__' => [$clientHost]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testOriginOnEndpointAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = ['__dummy_another_route__' => [], '__dummy_route__' => [$clientOrigin]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testRefererOnEndpointAllow(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $allowList = ['__dummy_another_route__' => [], '__dummy_route__' => [$clientReferer]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn($allowList);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testNotOnGlobalDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = ['__dummy_another__'];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn($denyList);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn([]);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testIpOnGlobalDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = [$clientIp];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn($denyList);
        $this->config->expects($this->never())->method('getAccessDenyEndpoints');

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testHostOnGlobalDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = [$clientHost];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn($denyList);
        $this->config->expects($this->never())->method('getAccessDenyEndpoints');

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testOriginOnGlobalDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = [$clientOrigin];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn($denyList);
        $this->config->expects($this->never())->method('getAccessDenyEndpoints');

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testRefererOnGlobalDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = [$clientReferer];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn($denyList);
        $this->config->expects($this->never())->method('getAccessDenyEndpoints');

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testNotOnEndpointDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = ['__dummy_another_route__' => [], '__dummy_route__' => ['__dummy_another__']];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn($denyList);

        $this->sut->onKernelController($this->event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testIpOnEndpointDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = ['__dummy_route__' => ['__dummy_another__', $clientIp]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn($denyList);

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testHostOnEndpointDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = ['__dummy_route__' => ['__dummy_another__', $clientHost]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn($denyList);

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testOriginOnEndpointDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = ['__dummy_route__' => ['__dummy_another__', $clientOrigin]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn($denyList);

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testRefererOnEndpointDeny(): void
    {
        $route = '__dummy_route__';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = ['__dummy_route__' => ['__dummy_another__', $clientReferer]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn($denyList);

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testRouteToUnderscore(): void
    {
        $route = '--dummy-route--';
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $denyList = ['__dummy_route__' => ['__dummy_another__', $clientHost]];

        $this->configTest($route, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->once())->method('getAccessAllowGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessAllowEndpoints')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyGlobals')->willReturn([]);
        $this->config->expects($this->once())->method('getAccessDenyEndpoints')->willReturn($denyList);

        try {
            $this->sut->onKernelController($this->event);
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $exception->getStatusCode());
            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelController
     * @covers ::allowGlobals
     * @covers ::allowEndpoint
     * @covers ::denyGlobals
     * @covers ::denyEndpoint
     */
    public function testMissingRouteOnRequest(): void
    {
        $clientIp = '__dummy_ip__';
        $clientHost = '__dummy_host__';
        $clientOrigin = '__dummy_origin__';
        $clientReferer = '__dummy_referer__';

        $this->configTest(false, $clientIp, $clientHost, $clientOrigin, $clientReferer);

        $this->config->expects($this->never())->method('getAccessAllowGlobals');
        $this->config->expects($this->never())->method('getAccessAllowEndpoints');
        $this->config->expects($this->never())->method('getAccessDenyGlobals');
        $this->config->expects($this->never())->method('getAccessDenyEndpoints');

        $this->expectExceptionObject((new Exception(Response::HTTP_INTERNAL_SERVER_ERROR))->addError(
            new Error(ErrorCode::UNKNOWN_ROUTE, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::UNKNOWN_ROUTE])
        ));

        $this->sut->onKernelController($this->event);
    }

    /**
     * @param bool|string $route
     * @param string $clientIp
     * @param string $clientHost
     * @param string $clientOrigin
     * @param string $clientReferer
     * @return void
     */
    private function configTest(
        bool|string $route,
        string $clientIp,
        string $clientHost,
        string $clientOrigin,
        string $clientReferer,
    ): void {
        $this->request->attributes = new ParameterBag(['_route' => $route]);
        $this->request->server = new ServerBag(['REMOTE_ADDR' => $clientIp, 'REMOTE_HOST' => $clientHost]);
        $this->request->headers = new HeaderBag(['origin' => $clientOrigin, 'referer' => $clientReferer]);

        $this->event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            function () {
            },
            $this->request,
            0
        );
    }
}
