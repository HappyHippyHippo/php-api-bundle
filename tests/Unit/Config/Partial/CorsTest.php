<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\Cors;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\Cors */
class CorsTest extends TestCase
{
    /**
     * @param string $envName
     * @param string $envValue
     * @param array<string, array<string, bool|string>> $config
     * @param string $path
     * @param bool|string $expected
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
        bool|string $expected
    ): void {
        putenv($envName . '=' . $envValue);
        $partial = new Cors();
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
                'envName' => 'HIPPY_CORS_ENABLED',
                'envValue' => 'true',
                'config' => $this->config(),
                'path' => 'cors.enabled',
                'expected' => true,
            ],
            'version' => [
                'envName' => 'HIPPY_CORS_ORIGIN',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'cors.origin',
                'expected' => '__dummy_env_value__',
            ],
        ];
    }

    /**
     * @param array<string, array<string, bool|string>> $config
     * @param string $path
     * @param bool|string $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadConfigDataTests
     */
    public function testLoadConfigData(array $config, string $path, bool|string $expected): void
    {
        $partial = new Cors();
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
            'name' => [
                'config' => $this->config(),
                'path' => 'cors.enabled',
                'expected' => $config['cors']['enabled'],
            ],
            'version' => [
                'config' => $this->config(),
                'path' => 'cors.origin',
                'expected' => $config['cors']['origin'],
            ],
        ];
    }

    /**
     * @param string $path
     * @param bool|string $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadDefaultDataTests
     */
    public function testLoadDefaultData(string $path, bool|string $expected): void
    {
        $partial = new Cors();
        $partial->load([]);

        $this->assertEquals($expected, $partial->get($path));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForLoadDefaultDataTests(): array
    {
        return [
            'name' => [
                'path' => 'cors.enabled',
                'expected' => false,
            ],
            'version' => [
                'path' => 'cors.origin',
                'expected' => '*',
            ],
        ];
    }

    /**
     * @return array<string, array<string, bool|string>>
     */
    private function config(): array
    {
        return [
            'cors' => [
                'enabled' => true,
                'origin' => '__dummy_origin_value__',
            ],
        ];
    }
}
