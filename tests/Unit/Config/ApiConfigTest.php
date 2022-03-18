<?php

namespace Hippy\Api\Tests\Unit\Config;

use Hippy\Api\Config\ApiConfig;
use Hippy\Config\Config;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
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
        $base = $this->getMockBuilder(Config::class)
            ->addMethods(['getRoot'])
            ->disableOriginalConstructor()
            ->getMock();
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
     * @covers ::bool
     * @covers ::int
     * @covers ::float
     * @covers ::string
     * @covers ::array
     * @dataProvider getProvider
     */
    public function testPartialRequest(string $method, string $path, mixed $expected): void
    {
        $base = $this->createMock(Config::class);
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
     * @covers ::bool
     * @covers ::int
     * @covers ::float
     * @covers ::string
     * @covers ::array
     * @dataProvider getProvider
     */
    public function testPartialExceptionRequest(string $method, string $path, mixed $value, string $expected): void
    {
        $base = $this->createMock(Config::class);
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
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('jsonSerialize')->willReturn($serialized);
        $sut = new ApiConfig($base);

        $this->assertEquals($serialized, $sut->jsonSerialize());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::bool
     * @throws ReflectionException
     */
    public function testBool(): void
    {
        $path = '__dummy_path__';
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn(true);
        $sut = new ApiConfig($base);

        $method = new ReflectionMethod(ApiConfig::class, 'bool');
        $this->assertTrue($method->invoke($sut, $path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::bool
     * @throws ReflectionException
     */
    public function testBoolThrowsOnInvalidType(): void
    {
        $path = '__dummy_path__';
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn(123);
        $sut = new ApiConfig($base);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage($path . ' config value is not a boolean');

        $method = new ReflectionMethod(ApiConfig::class, 'bool');
        $method->invoke($sut, $path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::int
     * @throws ReflectionException
     */
    public function testInt(): void
    {
        $path = '__dummy_path__';
        $value = 123;
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn($value);
        $sut = new ApiConfig($base);

        $method = new ReflectionMethod(ApiConfig::class, 'int');
        $this->assertEquals($value, $method->invoke($sut, $path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::int
     * @throws ReflectionException
     */
    public function testIntThrowsOnInvalidType(): void
    {
        $path = '__dummy_path__';
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn(true);
        $sut = new ApiConfig($base);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage($path . ' config value is not an integer');

        $method = new ReflectionMethod(ApiConfig::class, 'int');
        $method->invoke($sut, $path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::float
     * @throws ReflectionException
     */
    public function testFloat(): void
    {
        $path = '__dummy_path__';
        $value = 123.456;
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn($value);
        $sut = new ApiConfig($base);

        $method = new ReflectionMethod(ApiConfig::class, 'float');
        $this->assertEquals($value, $method->invoke($sut, $path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::float
     * @throws ReflectionException
     */
    public function testFloatThrowsOnInvalidType(): void
    {
        $path = '__dummy_path__';
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn(true);
        $sut = new ApiConfig($base);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage($path . ' config value is not a float');

        $method = new ReflectionMethod(ApiConfig::class, 'float');
        $method->invoke($sut, $path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::string
     * @throws ReflectionException
     */
    public function testString(): void
    {
        $path = '__dummy_path__';
        $value = '__dummy_value__';
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn($value);
        $sut = new ApiConfig($base);

        $method = new ReflectionMethod(ApiConfig::class, 'string');
        $this->assertEquals($value, $method->invoke($sut, $path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::string
     * @throws ReflectionException
     */
    public function testStringThrowsOnInvalidType(): void
    {
        $path = '__dummy_path__';
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn(true);
        $sut = new ApiConfig($base);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage($path . ' config value is not a string');

        $method = new ReflectionMethod(ApiConfig::class, 'string');
        $method->invoke($sut, $path);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::array
     * @throws ReflectionException
     */
    public function testArray(): void
    {
        $path = '__dummy_path__';
        $value = ['__dummy_value__'];
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn($value);
        $sut = new ApiConfig($base);

        $method = new ReflectionMethod(ApiConfig::class, 'array');
        $this->assertEquals($value, $method->invoke($sut, $path));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::array
     * @throws ReflectionException
     */
    public function testArrayThrowsOnInvalidType(): void
    {
        $path = '__dummy_path__';
        $base = $this->createMock(Config::class);
        $base->expects($this->once())->method('get')->with($path)->willReturn(true);
        $sut = new ApiConfig($base);

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage($path . ' config value is not an array');

        $method = new ReflectionMethod(ApiConfig::class, 'array');
        $method->invoke($sut, $path);
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
