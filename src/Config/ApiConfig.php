<?php

namespace Hippy\Api\Config;

use DateTime;
use Exception;
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
        return $this->int('app.id');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getAppName(): string
    {
        return $this->string('app.name');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getAppVersion(): string
    {
        return $this->string('app.version');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isEndpointConfigEnabled(): bool
    {
        return $this->bool('endpoint.config.enabled');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isEndpointOpenApiEnabled(): bool
    {
        return $this->bool('endpoint.openapi.enabled');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getEndpointOpenApiSource(): string
    {
        return $this->string('endpoint.openapi.source');
    }

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getEndpointOpenApiServers(): array
    {
        return $this->array('endpoint.openapi.servers');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isCorsEnabled(): bool
    {
        return $this->bool('cors.enabled');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getCorsOrigin(): string
    {
        return $this->string('cors.origin');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isErrorTraceEnabled(): bool
    {
        return $this->bool('errors.trace.enabled');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogRequestEnabled(): bool
    {
        return $this->bool('log.request.enabled');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogRequestMessage(): string
    {
        return $this->string('log.request.message');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogRequestLevel(): string
    {
        return $this->string('log.request.level');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogResponseEnabled(): bool
    {
        return $this->bool('log.response.enabled');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogResponseMessage(): string
    {
        return $this->string('log.response.message');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogResponseLevel(): string
    {
        return $this->string('log.response.level');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogExceptionEnabled(): bool
    {
        return $this->bool('log.exception.enabled');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogExceptionMessage(): string
    {
        return $this->string('log.exception.message');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogExceptionLevel(): string
    {
        return $this->string('log.exception.level');
    }

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getAccessAllowGlobals(): array
    {
        return $this->array('access.allow.global');
    }

    /**
     * @return array<int|string, string[]>
     * @throws TypeError
     */
    public function getAccessAllowEndpoints(): array
    {
        return $this->array('access.allow.endpoints');
    }

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getAccessDenyGlobals(): array
    {
        return $this->array('access.deny.global');
    }

    /**
     * @return array<int|string, string[]>
     * @throws TypeError
     */
    public function getAccessDenyEndpoints(): array
    {
        return $this->array('access.deny.endpoints');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isHeaderVersionEnabled(): bool
    {
        return $this->bool('version.header.enabled');
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
     * @return bool
     */
    protected function bool(string $path): bool
    {
        $value = $this->config->get($path);
        if (!is_bool($value)) {
            throw new TypeError($path . ' config value is not a boolean');
        }
        return $value;
    }

    /**
     * @param string $path
     * @return int
     */
    protected function int(string $path): int
    {
        $value = $this->config->get($path);
        if (!is_int($value)) {
            throw new TypeError($path . ' config value is not an integer');
        }
        return $value;
    }

    /**
     * @param string $path
     * @return float
     */
    protected function float(string $path): float
    {
        $value = $this->config->get($path);
        if (!is_float($value)) {
            throw new TypeError($path . ' config value is not a float');
        }
        return $value;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function string(string $path): string
    {
        $value = $this->config->get($path);
        if (!is_string($value)) {
            throw new TypeError($path . ' config value is not a string');
        }
        return $value;
    }

    /**
     * @param string $path
     * @return array<int|string, mixed>
     */
    protected function array(string $path): array
    {
        $value = $this->config->get($path);
        if (!is_array($value)) {
            throw new TypeError($path . ' config value is not an array');
        }
        return $value;
    }

    /**
     * @param string $path
     * @return DateTime
     */
    protected function datetime(string $path): DateTime
    {
        $value = $this->string($path);
        try {
            return new Datetime($value);
        } catch (Exception) {
            throw new TypeError($path . ' invalid datetime initialization string : ' . $value);
        }
    }
}
