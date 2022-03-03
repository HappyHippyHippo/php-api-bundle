<?php

namespace Hippy\Api\Tests\Unit\Service\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Service\Base\IndexService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/** @coversDefaultClass \Hippy\Api\Service\Base\IndexService */
class IndexServiceTest extends TestCase
{
    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var RouterInterface&MockObject */
    private RouterInterface $router;

    /** @var IndexService */
    private IndexService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->sut = new IndexService($this->config, $this->router);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::process
     */
    public function testProcess(): void
    {
        $name = '__dummy_name__';
        $version = '__dummy_version__';

        $routeCollection = $this->createMock(RouteCollection::class);
        $routeCollection->expects($this->once())->method('all')->willReturn([]);

        $this->config->expects($this->once())->method('getAppName')->willReturn($name);
        $this->config->expects($this->once())->method('getAppVersion')->willReturn($version);
        $this->router->expects($this->once())->method('getRouteCollection')->willReturn($routeCollection);

        $response = $this->sut->process();
        $this->assertEquals($name, $response->getName());
        $this->assertEquals($version, $response->getVersion());
        $this->assertEquals([], $response->getRoutes());
    }
}
