<?php

namespace Hippy\Api\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class App extends AbstractPartial
{
    /** @var string */
    protected const DOMAIN = 'app';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'app.id' => -1,
            'app.name' => 'unknown',
            'app.version' => 'development',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('app.id', 'int', $config);
        $this->loadType('app.name', 'string', $config);
        $this->loadType('app.version', 'string', $config);

        return $this;
    }
}
