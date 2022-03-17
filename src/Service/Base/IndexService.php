<?php

namespace Hippy\Api\Service\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Model\Controller\Index\IndexResponse;
use Hippy\Api\Service\AbstractService;
use Symfony\Component\Routing\RouterInterface;

class IndexService extends AbstractService
{
    /**
     * @param ApiConfig $config
     * @param RouterInterface $router
     */
    public function __construct(
        protected ApiConfig $config,
        protected RouterInterface $router,
    ) {
    }

    /**
     * @return IndexResponse
     */
    public function process(): IndexResponse
    {
        return new IndexResponse(
            $this->config->getAppName(),
            $this->config->getAppVersion(),
            $this->router->getRouteCollection()
        );
    }
}
