<?php

namespace Hippy\Api\Validator;

use Hippy\Api\Transformer\Validator\TransformerInterface;
use Hippy\Error\ErrorCollection;
use Hippy\Exception\Exception;
use Hippy\Model\Model;
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
     * @param Model $model
     * @param TransformerInterface $transformer
     * @return Model
     */
    protected function process(
        Model $model,
        TransformerInterface $transformer,
    ): Model {
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
