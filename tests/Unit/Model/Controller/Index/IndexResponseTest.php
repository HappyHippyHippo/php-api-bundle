<?php

namespace Hippy\Api\Tests\Unit\Model\Controller\Index;

use Hippy\Api\Model\Controller\Index\IndexResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/** @coversDefaultClass \Hippy\Api\Model\Controller\Index\IndexResponse */
class IndexResponseTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $name = '_service_name__';
        $version = '_service_version__';
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeCollection->expects($this->once())->method('all')->willReturn([]);

        $sut = new IndexResponse($name, $version, $routeCollection);

        $this->assertEquals($name, $sut->getName());
        $this->assertEquals($version, $sut->getVersion());
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testRoutes(): void
    {
        $name = '_service_name__';
        $version = '_service_version__';
        $expectedRoutes = [
            'route.1' => '[GET] /route1',
            'route.2.get' => '[GET] /route2',
            'route.2.post' => '[POST] /route2',
        ];

        $routeCreator = function (string $path, array $methods) {
            $mock = $this->createMock(Route::class);
            $mock->expects($this->once())->method('getPath')->willReturn($path);
            $mock->expects($this->once())->method('getMethods')->willReturn($methods);

            return $mock;
        };

        $routes = [
            'route.1' => $routeCreator('/route1', ['GET']),
            'route.2.get' => $routeCreator('/route2', ['GET']),
            'route.2.post' => $routeCreator('/route2', ['POST']),
        ];

        $routeCollection = $this->createMock(RouteCollection::class);
        $routeCollection->expects($this->once())->method('all')->willReturn($routes);

        $sut = new IndexResponse($name, $version, $routeCollection);
        $this->assertEquals($expectedRoutes, $sut->getRoutes());
    }
}
