<?php

namespace Hippy\Api\Transformer\Logging\Decorator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HeaderCleanerDecorator extends AbstractDecorator
{
    private const HEADER_AUTHORIZATION = 'authorization';

    /**
     * @param array<string, mixed> $data
     * @param Request $request
     * @return array<string, mixed>
     */
    public function request(array $data, Request $request): array
    {
        return $this->cleanHeaders('request', $data);
    }

    /**
     * @param array<string, mixed> $data
     * @param Request $request
     * @param Response $response
     * @return array<string, mixed>
     */
    public function response(array $data, Request $request, Response $response): array
    {
        return $this->cleanHeaders('request', $this->cleanHeaders('response', $data));
    }

    /**
     * @param array<string, mixed> $data
     * @param Request $request
     * @param Throwable $exception
     * @return array<string, mixed>
     */
    public function exception(array $data, Request $request, Throwable $exception): array
    {
        return $this->cleanHeaders('request', $data);
    }

    /**
     * @param string $section
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function cleanHeaders(string $section, array $data): array
    {
        if (
            isset($data[$section])
            && is_array($data[$section])
            && isset($data[$section]['headers'])
            && is_array($data[$section]['headers'])
        ) {
            $headers = &$data[$section]['headers'];
            foreach ($headers as $key => $value) {
                if (is_array($value) && count($value) === 1) {
                    $headers[$key] = reset($value);
                }
                if (is_string($key) && self::HEADER_AUTHORIZATION == strtolower($key)) {
                    unset($headers[$key]);
                }
            }
        }

        return $data;
    }
}
