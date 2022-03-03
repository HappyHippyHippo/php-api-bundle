<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Version extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'version';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'version.header.enabled' => true,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('version.header.enabled', 'bool', $config);

        return $this;
    }
}
