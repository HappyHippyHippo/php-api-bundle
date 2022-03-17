<?php

namespace Hippy\Api\Transformer\Validator;

use Hippy\Error\ErrorCollection;
use Hippy\Error\Error;
use Symfony\Component\Validator\ConstraintViolationInterface;

interface TransformerInterface
{
    /**
     * @param ErrorCollection|null $errors
     * @return int
     */
    public function getStatusCode(?ErrorCollection $errors = null): int;

    /**
     * @param ConstraintViolationInterface $violation
     * @return Error|null
     */
    public function transform(ConstraintViolationInterface $violation): ?Error;
}
