<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Access extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'access';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'access.allow.global' => [],
            'access.allow.endpoints' => [],
            'access.deny.global' => [],
            'access.deny.endpoints' => [],
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('access.allow.global', 'array', $config);
        $this->loadType('access.allow.endpoints', 'array', $config);
        $this->loadType('access.deny.global', 'array', $config);
        $this->loadType('access.deny.endpoints', 'array', $config);

        return $this;
    }
}
