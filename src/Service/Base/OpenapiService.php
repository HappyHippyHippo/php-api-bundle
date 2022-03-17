<?php

namespace Hippy\Api\Service\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Service\AbstractService;
use Hippy\Api\Service\Base\OpenApi\ReaderInterface;
use Hippy\Api\Service\Base\OpenApi\WriterInterface;
use Hippy\Api\Transformer\OpenApi\TransformerInterface;

class OpenapiService extends AbstractService
{
    /** @var TransformerInterface[] */
    protected array $transformers;

    /**
     * @param ApiConfig $config
     * @param ReaderInterface $reader
     * @param WriterInterface $writer
     * @param TransformerInterface[] $transformers
     */
    public function __construct(
        protected ApiConfig $config,
        protected ReaderInterface $reader,
        protected WriterInterface $writer,
        iterable $transformers = [],
    ) {
        $this->transformers = [];
        foreach ($transformers as $transformer) {
            if ($transformer instanceof TransformerInterface) {
                $this->transformers[] = $transformer;
            }
        }
    }

    /**
     * @return string
     */
    public function process(): string
    {
        $content = $this->reader->read($this->config->getRoot() . $this->config->getEndpointOpenApiSource());

        foreach ($this->transformers as $transformer) {
            $content = $transformer->transform($content);
        }

        return $this->writer->write($content);
    }
}
