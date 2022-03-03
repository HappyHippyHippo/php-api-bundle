<?php

namespace Hippy\Api\Service\Base;

use Hippy\Api\Model\Controller\Config\ConfigResponse;
use Hippy\Api\Service\AbstractService;
use Hippy\Config\ConfigInterface;

class ConfigService extends AbstractService
{
    /**
     * @param ConfigInterface $config
     */
    public function __construct(protected ConfigInterface $config)
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
