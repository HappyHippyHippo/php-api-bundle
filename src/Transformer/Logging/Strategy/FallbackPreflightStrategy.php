<?php

namespace Hippy\Api\Transformer\Logging\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FallbackPreflightStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('any', Response::HTTP_NO_CONTENT, -5);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        $route = $request->attributes->get('_route') ?? '';
        if ($route && is_string($route)) {
            return str_ends_with($route, '.preflight');
        }
        return false;
    }
}
