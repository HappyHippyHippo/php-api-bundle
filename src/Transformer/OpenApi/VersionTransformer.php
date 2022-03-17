<?php

namespace Hippy\Api\Transformer\OpenApi;

use Hippy\Api\Config\ApiConfig;

class VersionTransformer implements TransformerInterface
{
    /**
     * @param ApiConfig $config
     */
    public function __construct(protected ApiConfig $config)
    {
    }

    /**
     * @param object $content
     * @return object
     */
    public function transform(object $content): object
    {
        if (
            property_exists($content, 'info')
            && is_object($content->info)
            && property_exists($content->info, 'version')
        ) {
            $content->info->version = $this->config->getAppVersion();
        }

        return $content;
    }
}
