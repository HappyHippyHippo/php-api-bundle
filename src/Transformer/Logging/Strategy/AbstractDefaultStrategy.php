<?php

namespace Hippy\Api\Transformer\Logging\Strategy;

use Hippy\Api\Transformer\Logging\Decorator\HeaderCleanerDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectRequestDeltaDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectResponseBodyDecorator;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractDefaultStrategy extends AbstractStrategy
{
    /**
     * @param string $acceptedRoute
     * @param int $expectedStatusCode
     * @param int $priority
     */
    public function __construct(
        string $acceptedRoute,
        int $expectedStatusCode = Response::HTTP_OK,
        int $priority = 0
    ) {
        $decorators = [
            new HeaderCleanerDecorator(),
            new InjectRequestDeltaDecorator(),
            new InjectResponseBodyDecorator($expectedStatusCode),
        ];

        parent::__construct($acceptedRoute, $decorators, $priority);
    }
}
