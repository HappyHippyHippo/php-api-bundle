<?php

namespace Hippy\Api\Service\Base;

use Hippy\Api\Model\Controller\Config\ConfigResponse;
use Hippy\Api\Service\AbstractService;
use Hippy\Config\Config;

class ConfigService extends AbstractService
{
    /**
     * @param Config $config
     */
    public function __construct(protected Config $config)
    {
    }

    /**
     * @return ConfigResponse
     */
    public function process(): ConfigResponse
    {
        return new ConfigResponse($this->config);
    }
}
