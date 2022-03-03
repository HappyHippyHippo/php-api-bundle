<?php

namespace Hippy\Api\Model\Controller;

interface OrgRequestModelInterface extends RequestModelInterface
{
    public function getHeaderOrgId(): int;
}
