<?php

namespace Hippy\Api\Transformer\Logging\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FallbackStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('any', Response::HTTP_OK, -10);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return true;
    }
}
