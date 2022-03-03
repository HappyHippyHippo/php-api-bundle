<?php

namespace Hippy\Api\Model\Controller;

interface AuthOrgRequestModelInterface extends OrgRequestModelInterface
{
    /**
     * @return string
     */
    public function getHeaderAuthTokenId(): string;

    /**
     * @return int
     */
    public function getHeaderAuthUserId(): int;

    /**
     * @return string
     */
    public function getHeaderAuthUserUuid(): string;

    /**
     * @return string
     */
    public function getHeaderAuthUserEmail(): string;
}
