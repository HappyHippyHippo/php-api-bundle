<?php

namespace Hippy\Api\Model\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class OrgRequestModel extends RequestModel implements OrgRequestModelInterface
{
    /**
     * @return string|null
     * @Assert\NotBlank (message = "x-organization-id header must be present")
     * @Assert\Regex(pattern = "/^\d+$/", message = "x-organization-id header must be an integer")
     * @Assert\Positive(message = "x-organization-id header must be a positive integer")
     */
    protected ?string $headerOrgId;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->headerOrgId = $this->searchHeader('x-organization-id');
    }

    /**
     * @return int
     */
    public function getHeaderOrgId(): int
    {
        return (int) ($this->headerOrgId ?? 0);
    }
}
