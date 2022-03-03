<?php

namespace Hippy\Api\Service\Base\Check;

use Doctrine\DBAL\Connection;
use Hippy\Api\Model\Controller\Check\CheckResponse;
use Exception;

abstract class AbstractDatabaseConnectionCheck implements CheckInterface
{
    /** @var string  */
    protected const CONNECTION_SUCCESS_MESSAGE = 'connection established successfully';

    /**
     * @param string $name
     * @param Connection $connection
     */
    public function __construct(
        protected string $name,
        protected Connection $connection,
    ) {
    }

    /**
     * @param CheckResponse $response
     * @return bool
     */
    public function deepCheck(CheckResponse $response): bool
    {
        return $this->shallowCheck($response);
    }

    /**
     * @param CheckResponse $response
     * @return bool
     */
    public function shallowCheck(CheckResponse $response): bool
    {
        $success = true;
        $message = self::CONNECTION_SUCCESS_MESSAGE;

        try {
            $this->connection->connect();
        } catch (Exception $exception) {
            $success = false;
            $message = $exception->getMessage();
        }

        $response->addCheck($this->name, $success, $message);

        return $success;
    }
}
