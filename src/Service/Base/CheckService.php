<?php

namespace Hippy\Api\Service\Base;

use Hippy\Api\Model\Controller\Check\CheckRequest;
use Hippy\Api\Model\Controller\Check\CheckResponse;
use Hippy\Api\Service\AbstractService;
use Hippy\Api\Service\Base\Check\CheckInterface;
use Hippy\Exception\Exception;
use InvalidArgumentException;

class CheckService extends AbstractService
{
    /** @var CheckInterface[] */
    protected array $checks;

    /**
     * @param CheckInterface[] $checks
     */
    public function __construct(iterable $checks = [])
    {
        $this->checks = [];
        foreach ($checks as $check) {
            if (!($check instanceof CheckInterface)) {
                throw new InvalidArgumentException('invalid CheckInterface instance');
            }
            $this->checks[] = $check;
        }
    }

    /**
     * @param CheckRequest $request
     * @return CheckResponse
     */
    public function check(CheckRequest $request): CheckResponse
    {
        $isDeep = $request->isDeep();

        $response = new CheckResponse();
        $success = array_reduce(
            $this->checks,
            function (bool $success, CheckInterface $check) use ($response, $isDeep) {
                return ($isDeep ? $check->deepCheck($response) : $check->shallowCheck($response)) && $success;
            },
            true
        );

        if (!$success) {
            throw (new Exception())->setData($response);
        }

        return $response;
    }
}
