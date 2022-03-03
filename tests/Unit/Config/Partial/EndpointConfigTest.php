<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\EndpointConfig;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\EndpointConfig */
class EndpointConfigTest extends TestCase
{
    /**
     * @param string $envName
     * @param string $envValue
     * @param array<string, array<string, array<string, bool>>> $config
     * @param string $path
     * @param bool $expected
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
        bool $expected
    ): void {
        putenv($envName . '=' . $envValue);
        $partial = new EndpointConfig();
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
            'tries' => [
                'envName' => 'HIPPY_ENDPOINT_CONFIG_ENABLED',
                'envValue' => 'true',
                'config' => $this->config(),
                'path' => 'endpoint.config.enabled',
                'expected' => true,
            ],
        ];
    }

    /**
     * @param array<string, array<string, array<string, bool>>> $config
     * @param string $path
     * @param bool $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadConfigDataTests
     */
    public function testLoadConfigData(array $config, string $path, bool $expected): void
    {
        $partial = new EndpointConfig();
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
            'tries' => [
                'config' => $config,
                'path' => 'endpoint.config.enabled',
                'expected' => $config['endpoint']['config']['enabled'],
            ],
        ];
    }

    /**
     * @param string $path
     * @param bool $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadDefaultDataTests
     */
    public function testLoadDefaultData(string $path, bool $expected): void
    {
        $partial = new EndpointConfig();
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
                'path' => 'endpoint.config.enabled',
                'expected' => false,
            ],
        ];
    }

    /**
     * @return array<string, array<string, array<string, bool>>>
     */
    private function config(): array
    {
        return [
            'endpoint' => [
                'config' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
