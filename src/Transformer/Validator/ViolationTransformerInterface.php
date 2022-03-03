<?php

namespace Hippy\Api\Transformer\Validator;

use Hippy\Error\ErrorCollection;
use Hippy\Error\ErrorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

interface ViolationTransformerInterface
{
    /**
     * @param ErrorCollection|null $errors
     * @return int
     */
    public function getStatusCode(?ErrorCollection $errors = null): int;

    /**
     * @param ConstraintViolationInterface $violation
     * @return ErrorInterface|null
     */
    public function transform(ConstraintViolationInterface $violation): ?ErrorInterface;
}
