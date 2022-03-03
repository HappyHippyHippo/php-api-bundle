<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Redis extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'redis';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'redis.dsn' => 'redis://ds-redis:6379/messages',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('redis.dsn', 'string', $config);

        return $this;
    }
}
