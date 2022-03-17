<?php

namespace Hippy\Api\Model\Controller\Check;

use Hippy\Api\Model\Controller\RequestModel;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method bool isDeep()
 */
class CheckRequest extends RequestModel
{
    /** @var bool|null */
    protected ?bool $deep;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->deep = (bool) $request->query->get('deep', true);
    }
}
