<?php

namespace Hippy\Api\Model\Controller;

use Hippy\Model\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method Request getRequest()
 * @method string|null getHeaderRequestId()
 */
class RequestModel extends Model
{
    /**
     * @var string|null
     * @Assert\NotBlank(message = "x-request-id header must be present")
     */
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
