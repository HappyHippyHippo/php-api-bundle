<?php

namespace Hippy\Api\Validator;

use Hippy\Api\Transformer\Validator\ViolationTransformerInterface;
use Hippy\Error\ErrorCollection;
use Hippy\Exception\Exception;
use Hippy\Model\ModelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @param SymfonyValidatorInterface $validator
     */
    public function __construct(protected SymfonyValidatorInterface $validator)
    {
    }

    /**
     * @param ModelInterface $model
     * @param ViolationTransformerInterface $transformer
     * @return ModelInterface
     */
    protected function process(
        ModelInterface $model,
        ViolationTransformerInterface $transformer,
    ): ModelInterface {
        $errors = $this->validator->validate($model);
        if ($errors->count()) {
            $collection = new ErrorCollection();

            foreach ($errors as $error) {
                $transformed = $transformer->transform($error);
                if (!is_null($transformed)) {
                    $collection->add($transformed);
                }
            }

            if ($collection->count()) {
                $exception = new Exception($transformer->getStatusCode($collection));
                $exception->addErrors($collection);
                throw $exception;
            }
        }

        return $model;
    }
}
