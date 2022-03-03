<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\EndpointOpenapi;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\EndpointOpenapi */
class EndpointOpenapiTest extends TestCase
{
    /**
     * @param string $envName
     * @param string $envValue
     * @param array<string, array<string, array<string, mixed>>> $config
     * @param string $path
     * @param bool|string|array<int, string> $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadEnvironmentDataTests
     */
    public function testLoadEnvironmentData(
        string $envName,
        string $envValue,
        array $config,
        string $path,
        bool|string|array $expected
    ): void {
        putenv($envName . '=' . $envValue);
        $partial = new EndpointOpenapi();
        $partial->load($config);
        putenv($envName . '=');

        $this->assertEquals($expected, $partial->get($path));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForLoadEnvironmentDataTests(): array
    {
        return [
            'enabled' => [
                'envName' => 'HIPPY_ENDPOINT_OPENAPI_ENABLED',
                'envValue' => 'true',
                'config' => $this->config(),
                'path' => 'endpoint.openapi.enabled',
                'expected' => true,
            ],
            'source' => [
                'envName' => 'HIPPY_ENDPOINT_OPENAPI_SOURCE',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'endpoint.openapi.source',
                'expected' => '__dummy_env_value__',
            ],
            'servers' => [
                'envName' => 'HIPPY_ENDPOINT_OPENAPI_SERVERS',
                'envValue' => '__dummy_env_value_1__,__dummy_env_value_2__',
                'config' => $this->config(),
                'path' => 'endpoint.openapi.servers',
                'expected' => ['__dummy_env_value_1__', '__dummy_env_value_2__'],
            ],
        ];
    }

    /**
     * @param array<string, array<string, array<string, mixed>>> $config
     * @param string $path
     * @param bool|string|array<int, string> $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadConfigDataTests
     */
    public function testLoadConfigData(array $config, string $path, bool|string|array $expected): void
    {
        $partial = new EndpointOpenapi();
        $partial->load($config);

        $this->assertEquals($expected, $partial->get($path));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForLoadConfigDataTests(): array
    {
        $config = $this->config();

        return [
            'enabled' => [
                'config' => $this->config(),
                'path' => 'endpoint.openapi.enabled',
                'expected' => $config['endpoint']['openapi']['enabled'],
            ],
            'source' => [
                'config' => $this->config(),
                'path' => 'endpoint.openapi.source',
                'expected' => $config['endpoint']['openapi']['source'],
            ],
            'servers' => [
                'config' => $this->config(),
                'path' => 'endpoint.openapi.servers',
                'expected' => $config['endpoint']['openapi']['servers'],
            ],
        ];
    }

    /**
     * @param string $path
     * @param bool|string|array<int, string> $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadDefaultDataTests
     */
    public function testLoadDefaultData(string $path, bool|string|array $expected): void
    {
        $partial = new EndpointOpenapi();
        $partial->load([]);

        $this->assertEquals($expected, $partial->get($path));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForLoadDefaultDataTests(): array
    {
        return [
            'enabled' => [
                'path' => 'endpoint.openapi.enabled',
                'expected' => false,
            ],
            'source' => [
                'path' => 'endpoint.openapi.source',
                'expected' => '/openapi/openapi.yaml',
            ],
            'servers' => [
                'path' => 'endpoint.openapi.servers',
                'expected' => [],
            ],
        ];
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function config(): array
    {
        return [
            'endpoint' => [
                'openapi' => [
                    'enabled' => true,
                    'source' => '__dummy_source_value__',
                    'servers' => ['__dummy_value_1__', '__dummy_value_2__'],
                ],
            ],
        ];
    }
}
