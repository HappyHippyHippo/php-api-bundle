<?php

namespace Hippy\Api\Model\Controller;

use Hippy\Model\Model;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method Request getRequest()
 * @method string getHeaderRequestId()
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

    /**
     * @param ParameterBag $bag
     * @param string $param
     * @param mixed|null $default
     * @return mixed
     */
    protected function searchBagBool(ParameterBag $bag, string $param, mixed $default = null): mixed
    {
        $value = $this->searchBag($bag, $param, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    /**
     * @param ParameterBag $bag
     * @param string $param
     * @param mixed|null $default
     * @return mixed
     */
    protected function searchBagInt(ParameterBag $bag, string $param, mixed $default = null): mixed
    {
        $value = $this->searchBag($bag, $param, $default);
        return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    /**
     * @param ParameterBag $bag
     * @param string $param
     * @param mixed|null $default
     * @return mixed
     */
    protected function searchBagFloat(ParameterBag $bag, string $param, mixed $default = null): mixed
    {
        $value = $this->searchBag($bag, $param, $default);
        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) ?? $value;
    }

    /**
     * @param ParameterBag $bag
     * @param string $param
     * @param mixed|null $default
     * @return mixed
     */
    protected function searchBag(ParameterBag $bag, string $param, mixed $default = null): mixed
    {
        try {
            return $bag->get($param, $default);
        } catch (BadRequestException) {
            return $bag->all($param);
        }
    }
}
