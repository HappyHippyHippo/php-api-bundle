<?php

namespace Hippy\Api\Config;

use Hippy\Model\ModelInterface;
use TypeError;

interface ApiConfigInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getRoot(): string;

    /**
     * @return int
     * @throws TypeError
     */
    public function getAppId(): int;

    /**
     * @return string
     * @throws TypeError
     */
    public function getAppName(): string;

    /**
     * @return string
     * @throws TypeError
     */
    public function getAppVersion(): string;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isEndpointConfigEnabled(): bool;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isEndpointOpenApiEnabled(): bool;

    /**
     * @return string
     * @throws TypeError
     */
    public function getEndpointOpenApiSource(): string;

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getEndpointOpenApiServers(): array;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isCorsEnabled(): bool;

    /**
     * @return string
     * @throws TypeError
     */
    public function getCorsOrigin(): string;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isErrorTraceEnabled(): bool;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogRequestEnabled(): bool;

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogRequestMessage(): string;

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogRequestLevel(): string;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogResponseEnabled(): bool;

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogResponseMessage(): string;

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogResponseLevel(): string;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isLogExceptionEnabled(): bool;

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogExceptionMessage(): string;

    /**
     * @return string
     * @throws TypeError
     */
    public function getLogExceptionLevel(): string;

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getAccessAllowGlobals(): array;

    /**
     * @return array<string, string[]>
     * @throws TypeError
     */
    public function getAccessAllowEndpoints(): array;

    /**
     * @return string[]
     * @throws TypeError
     */
    public function getAccessDenyGlobals(): array;

    /**
     * @return array<string, string[]>
     * @throws TypeError
     */
    public function getAccessDenyEndpoints(): array;

    /**
     * @return bool
     * @throws TypeError
     */
    public function isHeaderVersionEnabled(): bool;
}
