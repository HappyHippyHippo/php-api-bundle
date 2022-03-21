<?php

namespace Hippy\Api\Validator\Base;

use Hippy\Api\Model\Controller\Check\CheckRequest;
use Hippy\Api\Transformer\Controller\Base\CheckTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CheckValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param CheckTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private CheckTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return CheckRequest
     */
    public function validate(Request $request): CheckRequest
    {
        $request = new CheckRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
