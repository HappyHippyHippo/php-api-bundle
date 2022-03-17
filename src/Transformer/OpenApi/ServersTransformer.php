<?php

namespace Hippy\Api\Transformer\OpenApi;

use Hippy\Api\Config\ApiConfig;

class ServersTransformer implements TransformerInterface
{
    /**
     * @var array<int, array<string, string>>
     */
    protected array $servers;

    /**
     * @param ApiConfig $config
     */
    public function __construct(ApiConfig $config)
    {
        $this->servers = [];
        $servers = $config->getEndpointOpenApiServers();
        foreach ($servers as $server) {
            $this->servers[] = ['url' => $server];
        }
    }

    /**
     * @param object $content
     * @return object
     */
    public function transform(object $content): object
    {
        return $this->addEndpointLevelServers($this->addAppLevelServers($content));
    }

    /**
     * @param object $content
     * @return object
     */
    private function addAppLevelServers(object $content): object
    {
        if (property_exists($content, 'servers')) {
            $content->servers = $this->servers;
        }

        return $content;
    }

    /**
     * @param object $content
     * @return object
     */
    private function addEndpointLevelServers(object $content): object
    {
        if (property_exists($content, 'paths') && is_iterable($content->paths)) {
            foreach ($content->paths as &$path) {
                if (property_exists($path, 'servers')) {
                    $path->servers = $this->servers;
                }
            }
        }

        return $content;
    }
}
