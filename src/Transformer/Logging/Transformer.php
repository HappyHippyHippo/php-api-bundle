<?php

namespace Hippy\Api\Transformer\Logging;

use Hippy\Api\Transformer\Logging\Strategy\StrategyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Transformer implements TransformerInterface
{
    /** @var StrategyInterface[] */
    protected array $strategies;

    /**
     * @param StrategyInterface[] $strategies
     */
    public function __construct(iterable $strategies = [])
    {
        $this->strategies = [];
        foreach ($strategies as $strategy) {
            if ($strategy instanceof StrategyInterface) {
                $this->strategies[] = $strategy;
            }
        }

        usort($this->strategies, function (StrategyInterface $strategy1, StrategyInterface $strategy2) {
            return $strategy2->priority() - $strategy1->priority();
        });
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function request(Request $request): array
    {
        $data = $this->getRequestData($request);

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($request)) {
                return $strategy->request($data, $request);
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return array<string, mixed>
     */
    public function response(Request $request, Response $response): array
    {
        $data = array_merge($this->getRequestData($request), $this->getResponseData($response));

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($request)) {
                return $strategy->response($data, $request, $response);
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return array<string, mixed>
     */
    public function exception(Request $request, Throwable $exception): array
    {
        $data = array_merge($this->getRequestData($request), $this->getExceptionData($exception));

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($request)) {
                return $strategy->exception($data, $request, $exception);
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @return array<string, array<string, mixed>>
     */
    protected function getRequestData(Request $request): array
    {
        return [
            'request' => [
                'method' => $request->getMethod(),
                'uri' => $request->getUri(),
                'clientIp' => $request->getClientIp(),
                'headers' => $request->headers->all(),
                'query' => $request->query->all(),
                'request' => $request->request->all(),
                'attributes' => $request->attributes->all(),
            ],
        ];
    }

    /**
     * @param Response $response
     * @return array<string, array<string, mixed>>
     */
    protected function getResponseData(Response $response): array
    {
        return [
            'response' => [
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ],
        ];
    }

    /**
     * @param Throwable $exception
     * @return array<string, array<string, mixed>>
     */
    protected function getExceptionData(Throwable $exception): array
    {
        return [
            'exception' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ],
        ];
    }
}
