<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Cors extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'cors';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'cors.enabled' => false,
            'cors.origin' => '*',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('cors.enabled', 'bool', $config);
        $this->loadType('cors.origin', 'string', $config);

        return $this;
    }
}
