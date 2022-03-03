<?php

namespace Hippy\Api\Tests\Unit\Transformer\OpenApi;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Transformer\OpenApi\ServersTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Transformer\OpenApi\ServersTransformer */
class ServersTransformerTest extends TestCase
{
    /** @var ApiConfigInterface&MockObject */
    protected ApiConfigInterface $config;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfigInterface::class);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::transform
     * @covers ::addAppLevelServers
     * @covers ::addEndpointLevelServers
     */
    public function testTransform(): void
    {
        $servers = ['__dummy_server_1__', '__dummy_server_2__'];
        $expected = [['url' => '__dummy_server_1__'], ['url' => '__dummy_server_2__']];

        $this->config->expects($this->once())->method('getEndpointOpenApiServers')->willReturn($servers);
        $sut = new ServersTransformer($this->config);

        $generatePath = function () {
            $path = (object) [[]];
            $path->servers = ['__dummy_content__'];
            return $path;
        };

        $paths = [$generatePath(), $generatePath(), $generatePath()];

        $content = (object) ['servers' => null];
        $content->paths = $paths;

        $result = $sut->transform($content);

        if (!property_exists($result, 'servers')) {
            $this->fail('servers property removed');
        }
        $this->assertEquals($expected, $result->servers);

        if (!property_exists($result, 'paths') || !is_array($result->paths)) {
            $this->fail('paths property removed');
        }
        foreach ($result->paths as $path) {
            if (!property_exists($path, 'servers')) {
                $this->fail('path servers property removed');
            }
            $this->assertEquals($expected, $path->servers);
        }
    }
}
