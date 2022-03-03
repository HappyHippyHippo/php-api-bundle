<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\Version;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\Version */
class VersionTest extends TestCase
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
        $partial = new Version();
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
                'envName' => 'HIPPY_VERSION_ENABLED',
                'envValue' => 'false',
                'config' => $this->config(),
                'path' => 'version.header.enabled',
                'expected' => false,
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
        $partial = new Version();
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
                'config' => $config,
                'path' => 'version.header.enabled',
                'expected' => $config['version']['header']['enabled'],
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
        $partial = new Version();
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
                'path' => 'version.header.enabled',
                'expected' => true,
            ],
        ];
    }

    /**
     * @return array<string, array<string, array<string, bool>>>
     */
    private function config(): array
    {
        return [
            'version' => [
                'header' => [
                    'enabled' => false,
                ],
            ],
        ];
    }
}
