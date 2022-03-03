<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\Redis;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\Redis */
class RedisTest extends TestCase
{
    /**
     * @param string $envName
     * @param string $envValue
     * @param array<string, array<string, string>> $config
     * @param string $path
     * @param string $expected
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
        string $expected
    ): void {
        putenv($envName . '=' . $envValue);
        $partial = new Redis();
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
            'dsn' => [
                'envName' => 'HIPPY_REDIS_DSN',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'redis.dsn',
                'expected' => '__dummy_env_value__',
            ],
        ];
    }

    /**
     * @param array<string, array<string, string>> $config
     * @param string $path
     * @param string $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadConfigDataTests
     */
    public function testLoadConfigData(array $config, string $path, string $expected): void
    {
        $partial = new Redis();
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
            'dsn' => [
                'config' => $config,
                'path' => 'redis.dsn',
                'expected' => $config['redis']['dsn'],
            ],
        ];
    }

    /**
     * @param string $path
     * @param string $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadDefaultDataTests
     */
    public function testLoadDefaultData(string $path, string $expected): void
    {
        $partial = new Redis();
        $partial->load([]);

        $this->assertEquals($expected, $partial->get($path));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForLoadDefaultDataTests(): array
    {
        return [
            'dsn' => ['path' => 'redis.dsn', 'expected' => 'redis://ds-redis:6379/messages'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function config(): array
    {
        return [
            'redis' => [
                'dsn' => '__dummy_dsn_value__',
            ],
        ];
    }
}
