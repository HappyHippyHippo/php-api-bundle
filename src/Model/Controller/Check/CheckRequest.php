<?php

namespace Hippy\Api\Model\Controller\Check;

use Hippy\Api\Model\Controller\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method bool isDeep()
 */
class CheckRequest extends RequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type = "bool", message = "deep parameter must be a boolean")
     */
    protected mixed $deep;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->deep = $this->searchBagBool($request->query, 'deep', false);
    }
}
