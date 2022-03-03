<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class EndpointConfig extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'endpoint.config';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'endpoint.config.enabled' => false,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('endpoint.config.enabled', 'bool', $config);

        return $this;
    }
}
