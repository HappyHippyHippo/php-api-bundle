<?php

namespace Hippy\Api\Config;

use Hippy\Config\Config;
use Hippy\Model\Model;
use TypeError;

class ApiConfig extends Model
{
    /**
     * @param Config $config
     */
    public function __construct(protected Config $config)
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->config->getRoot();
    }

    /**
     * @return int
     * @throws TypeError
     */
    public function getAppId(): int
    {
        $value = $this->get('app.id');
        if (!is_int($value)) {
            throw new TypeError('app.id config value is not an integer');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getAppName(): string
    {
        $value = $this->get('app.name');
        if (!is_string($value)) {
            throw new TypeError('app.name config value is not a string');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getAppVersion(): string
    {
        $value = $this->get('app.version');
        if (!is_string($value)) {
            throw new TypeError('app.version config value is not a string');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isEndpointConfigEnabled(): bool
    {
        $value = $this->get('endpoint.config.enabled');
        if (!is_bool($value)) {
            throw new TypeError('endpoint.config.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isEndpointOpenApiEnabled(): bool
    {
        $value = $this->get('endpoint.openapi.enabled');
        if (!is_bool($value)) {
            throw new TypeError('endpoint.openapi.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getEndpointOpenApiSource(): string
    {
        $value = $this->get('endpoint.openapi.source');
        if (!is_string($value)) {
            throw new TypeError('endpoint.openapi.source config value is not a string');
        }
        return $value;
    }

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getEndpointOpenApiServers(): array
    {
        $value = $this->get('endpoint.openapi.servers');
        if (!is_array($value)) {
            throw new TypeError('endpoint.openapi.servers config value is not an array');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isCorsEnabled(): bool
    {
        $value = $this->get('cors.enabled');
        if (!is_bool($value)) {
            throw new TypeError('cors.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getCorsOrigin(): string
    {
        $value = $this->get('cors.origin');
        if (!is_string($value)) {
            throw new TypeError('cors.origin config value is not a string');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isErrorTraceEnabled(): bool
    {
        $value = $this->get('errors.trace.enabled');
        if (!is_bool($value)) {
            throw new TypeError('errors.trace.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogRequestEnabled(): bool
    {
        $value = $this->get('log.request.enabled');
        if (!is_bool($value)) {
            throw new TypeError('log.request.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogRequestMessage(): string
    {
        $value = $this->get('log.request.message');
        if (!is_string($value)) {
            throw new TypeError('log.request.message config value is not a string');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogRequestLevel(): string
    {
        $value = $this->get('log.request.level');
        if (!is_string($value)) {
            throw new TypeError('log.request.level config value is not a string');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogResponseEnabled(): bool
    {
        $value = $this->get('log.response.enabled');
        if (!is_bool($value)) {
            throw new TypeError('log.response.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogResponseMessage(): string
    {
        $value = $this->get('log.response.message');
        if (!is_string($value)) {
            throw new TypeError('log.response.message config value is not a string');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogResponseLevel(): string
    {
        $value = $this->get('log.response.level');
        if (!is_string($value)) {
            throw new TypeError('log.response.level config value is not a string');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogExceptionEnabled(): bool
    {
        $value = $this->get('log.exception.enabled');
        if (!is_bool($value)) {
            throw new TypeError('log.exception.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogExceptionMessage(): string
    {
        $value = $this->get('log.exception.message');
        if (!is_string($value)) {
            throw new TypeError('log.exception.message config value is not a string');
        }
        return $value;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogExceptionLevel(): string
    {
        $value = $this->get('log.exception.level');
        if (!is_string($value)) {
            throw new TypeError('log.exception.level config value is not a string');
        }
        return $value;
    }

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getAccessAllowGlobals(): array
    {
        $value = $this->get('access.allow.global');
        if (!is_array($value)) {
            throw new TypeError('access.allow.global config value is not an array');
        }
        return $value;
    }

    /**
     * @return array<string, string[]>
     * @throws TypeError
     */
    public function getAccessAllowEndpoints(): array
    {
        $value = $this->get('access.allow.endpoints');
        if (!is_array($value)) {
            throw new TypeError('access.allow.endpoints config value is not an array');
        }
        return $value;
    }

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getAccessDenyGlobals(): array
    {
        $value = $this->get('access.deny.global');
        if (!is_array($value)) {
            throw new TypeError('access.deny.global config value is not an array');
        }
        return $value;
    }

    /**
     * @return array<string, string[]>
     * @throws TypeError
     */
    public function getAccessDenyEndpoints(): array
    {
        $value = $this->get('access.deny.endpoints');
        if (!is_array($value)) {
            throw new TypeError('access.deny.endpoints config value is not an array');
        }
        return $value;
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isHeaderVersionEnabled(): bool
    {
        $value = $this->get('version.header.enabled');
        if (!is_bool($value)) {
            throw new TypeError('version.header.enabled config value is not a boolean');
        }
        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->config->jsonSerialize();
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected function get(string $path): mixed
    {
        return $this->config->get($path);
    }
}
