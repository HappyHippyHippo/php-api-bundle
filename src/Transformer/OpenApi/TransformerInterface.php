<?php

namespace Hippy\Api\Transformer\OpenApi;

interface TransformerInterface
{
    /**
     * @param object $content
     * @return object
     */
    public function transform(object $content): object;
}
