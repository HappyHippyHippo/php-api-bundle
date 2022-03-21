<?php

namespace Hippy\Api\Transformer\Controller\Base;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class CheckTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct([
            'deep' => 1,
        ]);
    }
}
