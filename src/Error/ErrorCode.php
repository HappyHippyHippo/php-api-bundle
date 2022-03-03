<?php

namespace Hippy\Api\Error;

use Hippy\Error\ErrorCode as BaseErrorCode;

abstract class ErrorCode extends BaseErrorCode
{
    /** @var int */
    public const MALFORMED_JSON = 1;

    /** @var int */
    public const NOT_ENABLED = 2;

    /** @var int */
    public const NOT_ALLOWED = 3;

    /** @var int */
    public const UNKNOWN_ROUTE = 4;

    /** @var string[] */
    public const ERROR_TO_MESSAGE = [
        self::UNKNOWN => parent::ERROR_TO_MESSAGE[self::UNKNOWN],
        self::MALFORMED_JSON => 'Malformed request json',
        self::NOT_ENABLED => 'Not enabled',
        self::NOT_ALLOWED => 'Not allowed',
        self::UNKNOWN_ROUTE => 'Unable to determine route',
    ];
}
