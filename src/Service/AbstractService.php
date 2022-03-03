<?php

namespace Hippy\Api\Service;

use Hippy\Api\Error\ErrorCode;
use Hippy\Error\Error;
use Hippy\Error\ErrorCode as ErrorCodeAlias;
use Hippy\Error\ErrorCollection;
use Hippy\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractService
{
    /**
     * @return never
     */
    protected function throwsUnknown(): never
    {
        throw (new Exception(Response::HTTP_INTERNAL_SERVER_ERROR))->addError(
            new Error(ErrorCodeAlias::UNKNOWN, ErrorCode::ERROR_TO_MESSAGE[ErrorCodeAlias::UNKNOWN])
        );
    }

    /**
     * @param int $statusCode
     * @param int $error
     * @param string|null $message
     * @return never
     */
    protected function throws(int $statusCode, int $error, ?string $message = null): never
    {
        throw (new Exception($statusCode))->addError(
            new Error($error, $message ?? ErrorCode::ERROR_TO_MESSAGE[$error])
        );
    }

    /**
     * @param int $statusCode
     * @param array<int, int|string> $errors
     * @param array<int, string>|null $messages
     * @return never
     */
    protected function throwsMany(int $statusCode, array $errors, ?array $messages = null): never
    {
        $collection = new ErrorCollection();
        foreach ($errors as $error) {
            $collection->add(new Error($error, $messages[$error] ?? ErrorCode::ERROR_TO_MESSAGE[$error]));
        }

        throw (new Exception($statusCode))->addErrors($collection);
    }
}
