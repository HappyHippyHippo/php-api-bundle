<?php

namespace Hippy\Api\Service\Base\Check;

use Closure;
use Hippy\Api\Model\Controller\Check\CheckResponse;
use Exception;

abstract class AbstractApiConnectionCheck implements CheckInterface
{
    /** @var string */
    protected const CONNECTION_SUCCESS_MESSAGE = 'api connection checked successfully';

    /** @var string */
    protected const CONNECTION_FAIL_MESSAGE = 'api connection returned non-200 status code';

    /** @var string */
    protected const CONNECTION_NOT_TESTED = 'api connection not tested (shallow check)';

    /**
     * @param string $name
     * @param Closure $callback
     */
    public function __construct(
        protected string $name,
        protected Closure $callback,
    ) {
    }

    /**
     * @param CheckResponse $response
     * @return bool
     */
    public function deepCheck(CheckResponse $response): bool
    {
        $success = true;
        $message = self::CONNECTION_SUCCESS_MESSAGE;

        $extra = [];
        try {
            $extra = $this->callback->call($this);
        } catch (Exception) {
            $success = false;
            $message = self::CONNECTION_FAIL_MESSAGE;
        }

        if (!is_array($extra)) {
            $extra = [];
        }

        $response->addCheck($this->name, $success, $message, $extra);

        return $success;
    }

    /**
     * @param CheckResponse $response
     * @return bool
     */
    public function shallowCheck(CheckResponse $response): bool
    {
        $response->addCheck($this->name, true, self::CONNECTION_NOT_TESTED);

        return true;
    }
}
