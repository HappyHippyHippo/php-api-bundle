<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\Access;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\Access */
class AccessTest extends TestCase
{
    /**
     * @param string $envName
     * @param string $envValue
     * @param array<string, array<string, array<string, array<int, string>>>> $config
     * @param string $path
     * @param array<int, string> $expected
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
        array $expected,
    ): void {
        putenv($envName . '=' . $envValue);
        $partial = new Access();
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
            'allow.global' => [
                'envName' => 'HIPPY_ACCESS_ALLOW_GLOBAL',
                'envValue' => '__dummy_env_value_1__,__dummy_env_value_2__',
                'config' => $this->config(),
                'path' => 'access.allow.global',
                'expected' => ['__dummy_env_value_1__', '__dummy_env_value_2__'],
            ],
            'allow.endpoint' => [
                'envName' => 'HIPPY_ACCESS_ALLOW_ENDPOINTS',
                'envValue' => '__dummy_env_value_1__,__dummy_env_value_2__',
                'config' => $this->config(),
                'path' => 'access.allow.endpoints',
                'expected' => ['__dummy_env_value_1__', '__dummy_env_value_2__'],
            ],
            'deny.global' => [
                'envName' => 'HIPPY_ACCESS_DENY_GLOBAL',
                'envValue' => '__dummy_env_value_1__,__dummy_env_value_2__',
                'config' => $this->config(),
                'path' => 'access.deny.global',
                'expected' => ['__dummy_env_value_1__', '__dummy_env_value_2__'],
            ],
            'deny.endpoint' => [
                'envName' => 'HIPPY_ACCESS_DENY_ENDPOINTS',
                'envValue' => '__dummy_env_value_1__,__dummy_env_value_2__',
                'config' => $this->config(),
                'path' => 'access.deny.endpoints',
                'expected' => ['__dummy_env_value_1__', '__dummy_env_value_2__'],
            ],
        ];
    }

    /**
     * @param array<string, array<string, array<string, array<int, string>>>> $config
     * @param string $path
     * @param array<int, string> $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadConfigDataTests
     */
    public function testLoadConfigData(array $config, string $path, array $expected): void
    {
        $partial = new Access();
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
            'allow.global' => [
                'config' => $config,
                'path' => 'access.allow.global',
                'expected' => $config['access']['allow']['global'],
            ],
            'allow.endpoint' => [
                'config' => $config,
                'path' => 'access.allow.endpoints',
                'expected' => $config['access']['allow']['endpoints'],
            ],
            'deny.global' => [
                'config' => $config,
                'path' => 'access.deny.global',
                'expected' => $config['access']['deny']['global'],
            ],
            'deny.endpoint' => [
                'config' => $config,
                'path' => 'access.deny.endpoints',
                'expected' => $config['access']['deny']['endpoints'],
            ],
        ];
    }

    /**
     * @param string $path
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadDefaultDataTests
     */
    public function testLoadDefaultData(string $path): void
    {
        $partial = new Access();
        $partial->load([]);

        $this->assertEquals([], $partial->get($path));
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function providerForLoadDefaultDataTests(): array
    {
        return [
            'allow.global' => [
                'path' => 'access.allow.global',
            ],
            'allow.endpoint' => [
                'path' => 'access.allow.endpoints',
            ],
            'deny.global' => [
                'path' => 'access.deny.global',
            ],
            'deny.endpoint' => [
                'path' => 'access.deny.endpoints',
            ],
        ];
    }

    /**
     * @return array<string, array<string, array<string, array<int, string>>>>
     */
    private function config(): array
    {
        return [
            'access' => [
                'allow' => [
                    'global' => ['__dummy_allow_global_value_1__', '__dummy_allow_global_value_2__'],
                    'endpoints' => ['__dummy_allow_endpoint_value_1__', '__dummy_allow_endpoint_value_2__'],
                ],
                'deny' => [
                    'global' => ['__dummy_deny_global_value_1__', '__dummy_deny_global_value_2__'],
                    'endpoints' => ['__dummy_deny_endpoint_value_1__', '__dummy_deny_endpoint_value_2__'],
                ],
            ],
        ];
    }
}
