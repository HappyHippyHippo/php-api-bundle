<?php

namespace Hippy\Api\Tests\Unit\Config;

use Hippy\Api\Config\ApiConfig;
use Hippy\Config\Config as BaseConfig;
use Hippy\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use TypeError;

/** @coversDefaultClass \Hippy\Api\Config\ApiConfig */
class ApiConfigTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getRoot
     */
    public function testGetRoot(): void
    {
        $value = '__dummy_root__';
        $base = $this->createMock(ConfigInterface::class);
        $base->expects($this->once())->method('getRoot')->willReturn($value);
        $sut = new ApiConfig($base);

        $this->assertEquals($value, $sut->getRoot());
    }

    /**
     * @param string $method
     * @param string $path
     * @param mixed $expected
     * @return void
     * @covers ::__construct
     * @covers ::getRoot
     * @covers ::getAppId
     * @covers ::getAppName
     * @covers ::getAppVersion
     * @covers ::isEndpointConfigEnabled
     * @covers ::isEndpointOpenApiEnabled
     * @covers ::getEndpointOpenApiSource
     * @covers ::getEndpointOpenApiServers
     * @covers ::isCorsEnabled
     * @covers ::getCorsOrigin
     * @covers ::isErrorTraceEnabled
     * @covers ::isLogRequestEnabled
     * @covers ::getLogRequestMessage
     * @covers ::getLogRequestLevel
     * @covers ::isLogResponseEnabled
     * @covers ::getLogResponseMessage
     * @covers ::getLogResponseLevel
     * @covers ::isLogExceptionEnabled
     * @covers ::getLogExceptionMessage
     * @covers ::getLogExceptionLevel
     * @covers ::getAccessAllowGlobals
     * @covers ::getAccessAllowEndpoints
     * @covers ::getAccessDenyGlobals
     * @covers ::getAccessDenyEndpoints
     * @covers ::isHeaderVersionEnabled
     * @covers ::get
     * @dataProvider getProvider
     */
    public function testPartialRequest(string $method, string $path, mixed $expected): void
    {
        $base = $this->createMock(ConfigInterface::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn($expected);
        $sut = new ApiConfig($base);

        $this->assertEquals($expected, $sut->$method());
    }

    /**
     * @param string $method
     * @param string $path
     * @param mixed $value
     * @param string $expected
     * @return void
     * @covers ::__construct
     * @covers ::getRoot
     * @covers ::getAppId
     * @covers ::getAppName
     * @covers ::getAppVersion
     * @covers ::isEndpointConfigEnabled
     * @covers ::isEndpointOpenApiEnabled
     * @covers ::getEndpointOpenApiSource
     * @covers ::getEndpointOpenApiServers
     * @covers ::isCorsEnabled
     * @covers ::getCorsOrigin
     * @covers ::isErrorTraceEnabled
     * @covers ::isLogRequestEnabled
     * @covers ::getLogRequestMessage
     * @covers ::getLogRequestLevel
     * @covers ::isLogResponseEnabled
     * @covers ::getLogResponseMessage
     * @covers ::getLogResponseLevel
     * @covers ::isLogExceptionEnabled
     * @covers ::getLogExceptionMessage
     * @covers ::getLogExceptionLevel
     * @covers ::getAccessAllowGlobals
     * @covers ::getAccessAllowEndpoints
     * @covers ::getAccessDenyGlobals
     * @covers ::getAccessDenyEndpoints
     * @covers ::isHeaderVersionEnabled
     * @covers ::get
     * @dataProvider getProvider
     */
    public function testPartialExceptionRequest(string $method, string $path, mixed $value, string $expected): void
    {
        $base = $this->createMock(ConfigInterface::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn($value);
        $sut = new ApiConfig($base);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage($expected);
        $sut->$method();
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $serialized = ['__dummy_value__'];
        $base = $this->createMock(BaseConfig::class);
        $base->expects($this->once())->method('jsonSerialize')->willReturn($serialized);
        $sut = new ApiConfig($base);

        $this->assertEquals($serialized, $sut->jsonSerialize());
    }

    /**
     * @param string $provider
     * @return array<string, mixed>
     */
    public function getProvider(string $provider): array
    {
        $providers = Yaml::parseFile(sprintf('%s/%s.provider.yaml', dirname(__FILE__), basename(__FILE__, '.php')));
        if (!is_array($providers) || !isset($providers[$provider]) || !is_array($providers[$provider])) {
            $this->fail("invalid provider");
        }

        return $providers[$provider];
    }
}
