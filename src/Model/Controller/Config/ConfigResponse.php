<?php

namespace Hippy\Api\Model\Controller\Config;

use Hippy\Config\ConfigInterface;
use Hippy\Model\Model;

class ConfigResponse extends Model
{
    /**
     * @param ConfigInterface $config
     */
    public function __construct(protected ConfigInterface $config)
    {
        parent::__construct();
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }
}
