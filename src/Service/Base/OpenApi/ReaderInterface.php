<?php

namespace Hippy\Api\Service\Base\OpenApi;

use Symfony\Component\Yaml\Exception\ParseException;

interface ReaderInterface
{
    /**
     * @param string $path
     * @return object
     * @throws ParseException
     */
    public function read(string $path): object;
}
