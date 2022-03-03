<?php

namespace Hippy\Api\Transformer\Logging\Decorator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectRequestDeltaDecorator extends AbstractDecorator
{
    /**
     * @param array<string, mixed> $data
     * @param Request $request
     * @param Response $response
     * @return array<string, mixed>
     */
    public function response(array $data, Request $request, Response $response): array
    {
        $reqTime = (int) ($request->server->get('REQUEST_TIME_FLOAT') * 1000);
        $repTime = (int) (microtime(true) * 1000);
        $data['delta'] = $repTime - $reqTime;

        return $data;
    }
}
