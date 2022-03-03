<?php

namespace Hippy\Api\Service\Base\OpenApi;

use Symfony\Component\Yaml\Yaml;

class Writer implements WriterInterface
{
    /**
     * @param object $content
     * @return string
     * @codeCoverageIgnore
     */
    public function write(object $content): string
    {
        return Yaml::dump($content, 2, 4, Yaml::DUMP_OBJECT_AS_MAP);
    }
}
