<?php

namespace Hippy\Api\Transformer\Logging;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

interface TransformerInterface
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function request(Request $request): array;

    /**
     * @param Request $request
     * @param Response $response
     * @return array<string, mixed>
     */
    public function response(Request $request, Response $response): array;

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return array<string, mixed>
     */
    public function exception(Request $request, Throwable $exception): array;
}
