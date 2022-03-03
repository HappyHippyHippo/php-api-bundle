<?php

namespace Hippy\Api\Tests\Unit\Service\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Service\Base\OpenApi\Reader;
use Hippy\Api\Service\Base\OpenApi\Writer;
use Hippy\Api\Service\Base\OpenapiService;
use Hippy\Api\Transformer\OpenApi\TransformerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Api\Service\Base\OpenapiService */
class OpenapiServiceTest extends TestCase
{
    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var Reader&MockObject */
    private Reader $reader;

    /** @var Writer&MockObject */
    private Writer $writer;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->reader = $this->createMock(Reader::class);
        $this->writer = $this->createMock(Writer::class);
    }

    /**
     * @param mixed[] $transformers
     * @param TransformerInterface[] $expected
     * @return void
     * @covers ::__construct
     * @dataProvider provideForConstructTests
     */
    public function testConstruct(array $transformers, array $expected): void
    {
        $sut = $this->createService($transformers);

        $property = new ReflectionProperty(OpenapiService::class, 'transformers');
        $this->assertEquals($expected, $property->getValue($sut));
    }

    /**
     * @return array<string, mixed>
     */
    public function provideForConstructTests(): array
    {
        /**
         * @param int $count
         * @return array<int, TransformerInterface>
         */
        $construct = function (int $count) {
            $result = [];
            for ($i = 0; $i < $count; $i++) {
                $result[] = $this->createMock(TransformerInterface::class);
            }
            return $result;
        };

        /**
         * @param array $list
         * @return array<int, TransformerInterface>
         */
        $filter = function (array $list) {
            $result = [];
            foreach ($list as $transform) {
                if ($transform instanceof TransformerInterface) {
                    $result[] = $transform;
                }
            }
            return [$list, $result];
        };

        return [
            'no transformers' => [[], []],
            'no valid transformers' => $filter([(object) []]),
            'single valid transformers' => $filter($construct(1)),
            'multiple valid transformers' => $filter($construct(2)),
            'filter out non-transformers' => $filter(array_merge([(object) []], $construct(1), [(object) []])),
        ];
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::process
     */
    public function testProcess(): void
    {
        $root = '__dummy_root__';
        $path = '__dummy_path__';

        $content = (object) ['field' => '__dummy_data__'];
        $contentTransformed = '__dummy_content__';

        $transformers = [];
        for ($i = 0; $i < 3; $i++) {
            $transformer = $this->createMock(TransformerInterface::class);
            $transformer->expects($this->once())->method('transform')->with($content)->willReturn($content);
            $transformers[] = $transformer;
        }

        $this->config->expects($this->once())->method('getRoot')->willReturn($root);
        $this->config->expects($this->once())->method('getEndpointOpenapiSource')->willReturn($path);

        $sut = $this->createService($transformers);

        $this->reader->expects($this->once())->method('read')->with($root . $path)->willReturn($content);
        $this->writer->expects($this->once())->method('write')->with($content)->willReturn($contentTransformed);

        $this->assertSame($contentTransformed, $sut->process());
    }

    /**
     * @param mixed[] $transformers
     * @return OpenapiService&MockObject
     */
    private function createService(array $transformers = []): OpenapiService & MockObject
    {
        return $this->getMockBuilder(OpenapiService::class)
            ->setConstructorArgs([$this->config, $this->reader, $this->writer, $transformers])
            ->onlyMethods([])
            ->getMock();
    }
}
