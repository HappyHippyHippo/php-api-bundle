<?php

namespace Hippy\Api\Model\Controller;

use Hippy\Model\ModelInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestModelInterface extends ModelInterface
{
    /**
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * @return string|null
     */
    public function getHeaderRequestId(): ?string;
}
