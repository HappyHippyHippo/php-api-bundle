<?php

namespace Hippy\Api\Service\Base;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Model\Controller\Index\IndexResponse;
use Hippy\Api\Service\AbstractService;
use Symfony\Component\Routing\RouterInterface;

class IndexService extends AbstractService
{
    /**
     * @param ApiConfigInterface $config
     * @param RouterInterface $router
     */
    public function __construct(
        protected ApiConfigInterface $config,
        protected RouterInterface $router,
    ) {
    }

    /**
     * @return IndexResponse
     */
    public function process(): IndexResponse
    {
        return new IndexResponse(
            (string) $this->config->getAppName(),
            (string) $this->config->getAppVersion(),
            $this->router->getRouteCollection()
        );
    }
}
