<?php

namespace Hippy\Api\Tests\Unit\Config\Partial;

use Hippy\Api\Config\Partial\Log;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Config\Partial\Log */
class LogTest extends TestCase
{
    /**
     * @param string $envName
     * @param string $envValue
     * @param array<string, array<string, mixed>> $config
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
        $partial = new Log();
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
            'request.enabled' => [
                'envName' => 'HIPPY_LOG_REQUEST_ENABLED',
                'envValue' => 'false',
                'config' => $this->config(),
                'path' => 'log.request.enabled',
                'expected' => false,
            ],
            'request.message' => [
                'envName' => 'HIPPY_LOG_REQUEST_MESSAGE',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'log.request.message',
                'expected' => '__dummy_env_value__',
            ],
            'request.level' => [
                'envName' => 'HIPPY_LOG_REQUEST_LEVEL',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'log.request.level',
                'expected' => '__dummy_env_value__',
            ],
            'response.enabled' => [
                'envName' => 'HIPPY_LOG_RESPONSE_ENABLED',
                'envValue' => 'false',
                'config' => $this->config(),
                'path' => 'log.response.enabled',
                'expected' => false,
            ],
            'response.message' => [
                'envName' => 'HIPPY_LOG_RESPONSE_MESSAGE',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'log.response.message',
                'expected' => '__dummy_env_value__',
            ],
            'response.level' => [
                'envName' => 'HIPPY_LOG_RESPONSE_LEVEL',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'log.response.level',
                'expected' => '__dummy_env_value__',
            ],
            'exception.enabled' => [
                'envName' => 'HIPPY_LOG_EXCEPTION_ENABLED',
                'envValue' => 'false',
                'config' => $this->config(),
                'path' => 'log.exception.enabled',
                'expected' => false,
            ],
            'exception.message' => [
                'envName' => 'HIPPY_LOG_EXCEPTION_MESSAGE',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'log.exception.message',
                'expected' => '__dummy_env_value__',
            ],
            'exception.level' => [
                'envName' => 'HIPPY_LOG_EXCEPTION_LEVEL',
                'envValue' => '__dummy_env_value__',
                'config' => $this->config(),
                'path' => 'log.exception.level',
                'expected' => '__dummy_env_value__',
            ],
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $config
     * @param string $path
     * @param bool|string $expected
     * @return void
     * @covers ::__construct
     * @covers ::load
     * @dataProvider providerForLoadConfigDataTests
     */
    public function testLoadConfigData(array $config, string $path, bool|string $expected): void
    {
        $partial = new Log();
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
            'request.enabled' => [
                'config' => $config,
                'path' => 'log.request.enabled',
                'expected' => $config['log']['request']['enabled'],
            ],
            'request.message' => [
                'config' => $config,
                'path' => 'log.request.message',
                'expected' => $config['log']['request']['message'],
            ],
            'request.level' => [
                'config' => $config,
                'path' => 'log.request.level',
                'expected' => $config['log']['request']['level'],
            ],
            'response.enabled' => [
                'config' => $config,
                'path' => 'log.response.enabled',
                'expected' => $config['log']['response']['enabled'],
            ],
            'response.message' => [
                'config' => $config,
                'path' => 'log.response.message',
                'expected' => $config['log']['response']['message'],
            ],
            'response.level' => [
                'config' => $config,
                'path' => 'log.response.level',
                'expected' => $config['log']['response']['level'],
            ],
            'exception.enabled' => [
                'config' => $config,
                'path' => 'log.exception.enabled',
                'expected' => $config['log']['exception']['enabled'],
            ],
            'exception.message' => [
                'config' => $config,
                'path' => 'log.exception.message',
                'expected' => $config['log']['exception']['message'],
            ],
            'exception.level' => [
                'config' => $config,
                'path' => 'log.exception.level',
                'expected' => $config['log']['exception']['level'],
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
        $partial = new Log();
        $partial->load([]);

        $this->assertEquals($expected, $partial->get($path));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForLoadDefaultDataTests(): array
    {
        return [
            'request.enabled' => [
                'path' => 'log.request.enabled',
                'expected' => true,
            ],
            'request.message' => [
                'path' => 'log.request.message',
                'expected' => 'Request',
            ],
            'request.level' => [
                'path' => 'log.request.level',
                'expected' => 'info',
            ],
            'response.enabled' => [
                'path' => 'log.response.enabled',
                'expected' => true,
            ],
            'response.message' => [
                'path' => 'log.response.message',
                'expected' => 'Response',
            ],
            'response.level' => [
                'path' => 'log.response.level',
                'expected' => 'info',
            ],
            'exception.enabled' => [
                'path' => 'log.exception.enabled',
                'expected' => true,
            ],
            'exception.message' => [
                'path' => 'log.exception.message',
                'expected' => 'Exception',
            ],
            'exception.level' => [
                'path' => 'log.exception.level',
                'expected' => 'error',
            ],
        ];
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function config(): array
    {
        return [
            'log' => [
                'request' => [
                    'enabled' => true,
                    'message' => '__dummy_request_message_value__',
                    'level' => '__dummy_request_level_value__',
                ],
                'response' => [
                    'enabled' => true,
                    'message' => '__dummy_response_message_value__',
                    'level' => '__dummy_response_level_value__',
                ],
                'exception' => [
                    'enabled' => true,
                    'message' => '__dummy_exception_message_value__',
                    'level' => '__dummy_exception_level_value__',
                ],
            ],
        ];
    }
}
