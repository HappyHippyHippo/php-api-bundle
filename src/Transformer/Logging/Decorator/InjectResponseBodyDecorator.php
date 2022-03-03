<?php

namespace Hippy\Api\Transformer\Logging\Decorator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectResponseBodyDecorator extends AbstractDecorator
{
    /**
     * @param int $expectedStatusCode
     */
    public function __construct(protected int $expectedStatusCode = Response::HTTP_OK)
    {
    }

    /**
     * @return int
     */
    public function getExpectedStateCode(): int
    {
        return $this->expectedStatusCode;
    }

    /**
     * @param int $expectedStatusCode
     * @return $this
     */
    public function setExpectedStateCode(int $expectedStatusCode): InjectResponseBodyDecorator
    {
        $this->expectedStatusCode = $expectedStatusCode;
        return $this;
    }

    /**
     * @param array<string, mixed> $data
     * @param Request $request
     * @param Response $response
     * @return array<string, mixed>
     */
    public function response(array $data, Request $request, Response $response): array
    {
        if ($response->getStatusCode() != $this->expectedStatusCode) {
            if (isset($data['response']) && is_array($data['response'])) {
                $content = $response->getContent();
                if (is_string($content)) {
                    $data['response']['body'] = json_decode($content, true);
                }
            }
        }

        return $data;
    }
}
