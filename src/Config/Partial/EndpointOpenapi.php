<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class EndpointOpenapi extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'endpoint.openapi';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'endpoint.openapi.enabled' => false,
            'endpoint.openapi.source' => '/openapi/openapi.yaml',
            'endpoint.openapi.servers' => [],
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('endpoint.openapi.enabled', 'bool', $config);
        $this->loadType('endpoint.openapi.source', 'string', $config);
        $this->loadType('endpoint.openapi.servers', 'array', $config);

        return $this;
    }
}
