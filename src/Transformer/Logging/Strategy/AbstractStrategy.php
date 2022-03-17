<?php

namespace Hippy\Api\Transformer\Logging\Strategy;

use Hippy\Api\Transformer\Logging\Decorator\DecoratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var DecoratorInterface[] */
    protected array $decorators;

    /**
     * @param string $acceptedRoute
     * @param array<int, mixed> $decorators
     * @param int $priority
     */
    public function __construct(
        protected string $acceptedRoute,
        array $decorators = [],
        protected int $priority = 0,
    ) {
        $this->decorators = [];
        foreach ($decorators as $decorator) {
            if ($decorator instanceof DecoratorInterface) {
                $this->decorators[] = $decorator;
            }
        }
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') == $this->acceptedRoute;
    }

    /**
     * @param array<string, array<string, mixed>> $data
     * @param Request $request
     * @return array<string, mixed>
     */
    public function request(array $data, Request $request): array
    {
        foreach ($this->decorators as $decorator) {
            $data = $decorator->request($data, $request);
        }
        return $data;
    }

    /**
     * @param array<string, array<string, mixed>> $data
     * @param Request $request
     * @param Response $response
     * @return array<string, mixed>
     */
    public function response(array $data, Request $request, Response $response): array
    {
        foreach ($this->decorators as $decorator) {
            $data = $decorator->response($data, $request, $response);
        }
        return $data;
    }

    /**
     * @param array<string, array<string, mixed>> $data
     * @param Request $request
     * @param Throwable $exception
     * @return array<string, mixed>
     */
    public function exception(array $data, Request $request, Throwable $exception): array
    {
        foreach ($this->decorators as $decorator) {
            $data = $decorator->exception($data, $request, $exception);
        }
        return $data;
    }
}
