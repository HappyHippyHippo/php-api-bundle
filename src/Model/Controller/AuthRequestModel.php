<?php

namespace Hippy\Api\Model\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method string|null getHeaderAuthTokenId()
 * @method string|null getHeaderAuthUserId()
 * @method string|null getHeaderAuthUserEmail()
 */
class AuthRequestModel extends RequestModel
{
    /**
     * @return string|null
     * @Assert\NotBlank(message = "x-auth-token-id header must be present")
     */
    protected ?string $headerAuthTokenId;

    /**
     * @return string|null
     * @Assert\NotBlank(message = "x-auth-user-id header must be present")
     * @Assert\Regex(pattern = "/^\d+$/", message = "x-auth-user-id header must be an integer")
     * @Assert\Positive(message = "x-auth-user-id header must be a positive integer")
     */
    protected ?string $headerAuthUserId;

    /**
     * @return string|null
     * @Assert\NotBlank(message = "x-auth-user-email header must be present")
     * @Assert\Email(message = "x-auth-user-email header must be a valid email")
     */
    protected ?string $headerAuthUserEmail;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->headerAuthTokenId = $this->searchHeader('x-auth-token-id');
        $this->headerAuthUserId = $this->searchHeader('x-auth-user-id');
        $this->headerAuthUserEmail = $this->searchHeader('x-auth-user-email');
    }
}
