<?php

namespace Hippy\Api\Transformer\Logging\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

interface StrategyInterface
{
    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool;

    /**
     * @param array<string, array<string, mixed>> $data
     * @param Request $request
     * @return array<string, mixed>
     */
    public function request(array $data, Request $request): array;

    /**
     * @param array<string, array<string, mixed>> $data
     * @param Request $request
     * @param Response $response
     * @return array<string, mixed>
     */
    public function response(array $data, Request $request, Response $response): array;

    /**
     * @param array<string, array<string, mixed>> $data
     * @param Request $request
     * @param Throwable $exception
     * @return array<string, mixed>
     */
    public function exception(array $data, Request $request, Throwable $exception): array;
}
