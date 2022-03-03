<?php

namespace Hippy\Api\Model\Controller;

use Hippy\Model\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RequestModel extends Model implements RequestModelInterface
{
    /** @var string|null */
    protected ?string $headerRequestId;

    /**
     * @param Request $request
     */
    public function __construct(protected Request $request)
    {
        parent::__construct();

        $this->headerRequestId = $this->searchHeader('x-request-id');
        $this->addHideParser('request');
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string|null
     */
    public function getHeaderRequestId(): ?string
    {
        return $this->headerRequestId ?? null;
    }

    /**
     * @param string $header
     * @return string|null
     */
    protected function searchHeader(string $header): ?string
    {
        if ($this->request->headers == null) {
            return null;
        }

        $header = strtolower($header);
        foreach ($this->request->headers->all() as $key => $value) {
            if (strtolower((string) $key) == $header) {
                if (is_array($value)) {
                    $value = reset($value);
                }
                return (string) $value;
            }
        }
        return null;
    }
}
