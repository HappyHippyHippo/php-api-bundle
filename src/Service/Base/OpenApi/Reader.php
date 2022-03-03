<?php

namespace Hippy\Api\Service\Base\OpenApi;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Reader implements ReaderInterface
{
    /** @var array<string, object> $files */
    protected array $files;

    /**
     * @param string $path
     * @return object
     * @throws ParseException
     */
    public function read(string $path): object
    {
        $this->files = [];

        return $this->parseObject($path, $this->readFile($path));
    }

    /**
     * @param string $path
     * @param object $content
     * @return object
     * @throws ParseException
     */
    protected function parseObject(string $path, object $content): object
    {
        foreach (get_object_vars($content) as $prop => $value) {
            if ($prop == '$ref') {
                $ref = $this->ref($value, $path);
                unset($content->$prop);
                $content = (object) array_merge((array) $content, (array) $ref);
                continue;
            }

            if (is_object($value)) {
                $content->$prop = $this->parseObject($path, $value);
            }
            if (is_array($value)) {
                $content->$prop = $this->parseArray($path, $value);
            }
        }

        return $content;
    }

    /**
     * @param string $path
     * @param array<int, mixed> $content
     * @return array<int, mixed>
     * @throws ParseException
     */
    protected function parseArray(string $path, array $content): array
    {
        foreach ($content as $key => $value) {
            if (is_object($value)) {
                $content[$key] = $this->parseObject($path, $value);
            }
            if (is_array($value)) {
                $content[$key] = $this->parseArray($path, $value);
            }
        }

        return $content;
    }

    /**
     * @param string $ref
     * @param string $path
     * @return object
     * @throws ParseException
     */
    protected function ref(string $ref, string $path): object
    {
        if (str_starts_with($ref, '#')) {
            return $this->loadedRef($ref, $path);
        }
        return $this->remoteRef($ref, $path);
    }

    /**
     * @param string $ref
     * @param string $path
     * @return object
     * @throws ParseException
     */
    protected function loadedRef(string $ref, string $path): object
    {
        $it = $this->files[$path];
        foreach (explode('/', substr($ref, 1)) as $path) {
            if (!empty($path)) {
                if (!property_exists($it, $path)) {
                    throw new ParseException('unable to reference the openapi entry : ' . $ref);
                }
                $it = $it->$path;
            }
        }

        return $it;
    }

    /**
     * @param string $ref
     * @param string $path
     * @return object
     * @throws ParseException
     */
    protected function remoteRef(string $ref, string $path): object
    {
        $dir = dirname($path) . '/';
        $parts = explode('#', $ref);
        $path = $dir . $parts[0];

        $this->parseObject($path, $this->readFile($path));
        if (!array_key_exists(1, $parts)) {
            return $this->files[$path];
        }
        return $this->loadedRef('#' . $parts[1], $path);
    }

    /**
     * @param string $path
     * @return object
     * @throws ParseException
     */
    protected function readFile(string $path): object
    {
        if (!array_key_exists($path, $this->files)) {
            $this->files[$path] = $this->readYamlFile($path);
        }

        return $this->files[$path];
    }

    /**
     * @param string $path
     * @return object
     * @throws ParseException
     * @codeCoverageIgnore
     */
    protected function readYamlFile(string $path): object
    {
        $content = Yaml::parseFile($path, Yaml::PARSE_OBJECT_FOR_MAP);
        if (!is_object($content)) {
            throw new ParseException('unable to read openapi source');
        }
        return $content;
    }
}
