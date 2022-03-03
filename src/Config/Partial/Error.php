<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Error extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'errors';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'errors.trace.enabled' => false,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('errors.trace.enabled', 'bool', $config);

        return $this;
    }
}
