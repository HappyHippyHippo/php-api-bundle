<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\App;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\App */
class AppTest extends TestCase
{
    /**
     * @param string $envName
     * @param string $envValue
     * @param array<string, array<string, int|string>> $config
     * @param string $path
     * @param int|string $expected
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
        int|string $expected,
    ): void {
        putenv($envName . '=' . $envValue);
        $partial = new App();
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
            'id' => [
                'envName' => 'HIPPY_APP_ID',
                'envValue' => '2',
                'config' => $this->config(),
                'path' => 'app.id',
                'expected' => 2,
            ],
            'name' => [
                'envName' => 'HIPPY_APP_NAME',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'app.name',
                'expected' => '__dummy_env_value__',
            ],
            'version' => [
                'envName' => 'HIPPY_APP_VERSION',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'app.version',
                'expected' => '__dummy_env_value__',
            ],
        ];
    }

    /**
     * @param array<string, array<string, int|string>> $config
     * @param string $path
     * @param int|string $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadConfigDataTests
     */
    public function testLoadConfigData(array $config, string $path, int|string $expected): void
    {
        $partial = new App();
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
            'id' => [
                'config' => $this->config(),
                'path' => 'app.id',
                'expected' => $config['app']['id'],
            ],
            'name' => [
                'config' => $this->config(),
                'path' => 'app.name',
                'expected' => $config['app']['name'],
            ],
            'version' => [
                'config' => $this->config(),
                'path' => 'app.version',
                'expected' => $config['app']['version'],
            ],
        ];
    }

    /**
     * @param string $path
     * @param int|string $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadDefaultDataTests
     */
    public function testLoadDefaultData(string $path, int|string $expected): void
    {
        $partial = new App();
        $partial->load([]);

        $this->assertEquals($expected, $partial->get($path));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForLoadDefaultDataTests(): array
    {
        return [
            'id' => [
                'path' => 'app.id',
                'expected' => -1,
            ],
            'name' => [
                'path' => 'app.name',
                'expected' => 'unknown',
            ],
            'version' => [
                'path' => 'app.version',
                'expected' => 'development',
            ],
        ];
    }

    /**
     * @return array<string, array<string, int|string>>
     */
    private function config(): array
    {
        return [
            'app' => [
                'id' => 1,
                'name' => '__dummy_name_value__',
                'version' => '__dummy_version_value__',
            ],
        ];
    }
}
