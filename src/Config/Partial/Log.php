<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Log extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'log';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'log.request.enabled' => true,
            'log.request.message' => 'Request',
            'log.request.level' => 'info',
            'log.response.enabled' => true,
            'log.response.message' => 'Response',
            'log.response.level' => 'info',
            'log.exception.enabled' => true,
            'log.exception.message' => 'Exception',
            'log.exception.level' => 'error',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('log.request.enabled', 'bool', $config);
        $this->loadType('log.request.message', 'string', $config);
        $this->loadType('log.request.level', 'string', $config);
        $this->loadType('log.response.enabled', 'bool', $config);
        $this->loadType('log.response.message', 'string', $config);
        $this->loadType('log.response.level', 'string', $config);
        $this->loadType('log.exception.enabled', 'bool', $config);
        $this->loadType('log.exception.message', 'string', $config);
        $this->loadType('log.exception.level', 'string', $config);

        return $this;
    }
}
