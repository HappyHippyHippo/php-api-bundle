<?php

namespace Hippy\Api\Transformer\OpenApi;

use Hippy\Api\Config\ApiConfigInterface;

class VersionTransformer implements TransformerInterface
{
    /**
     * @param ApiConfigInterface $config
     */
    public function __construct(protected ApiConfigInterface $config)
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
