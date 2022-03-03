<?php

namespace Hippy\Api\Service\Base\OpenApi;

interface WriterInterface
{
    /**
     * @param object $content
     * @return string
     * @codeCoverageIgnore
     */
    public function write(object $content): string;
}
