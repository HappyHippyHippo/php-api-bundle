<?php

namespace Hippy\Api\Service\Base\Check;

use Hippy\Api\Model\Controller\Check\CheckResponse;

interface CheckInterface
{
    /**
     * @param CheckResponse $response
     * @return bool
     */
    public function deepCheck(CheckResponse $response): bool;

    /**
     * @param CheckResponse $response
     * @return bool
     */
    public function shallowCheck(CheckResponse $response): bool;
}
