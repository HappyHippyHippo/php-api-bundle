<?php

namespace Hippy\Api\Tests\Unit\Transformer\OpenApi;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Transformer\OpenApi\VersionTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Transformer\OpenApi\VersionTransformer */
class VersionTransformerTest extends TestCase
{
    /** @var ApiConfig&MockObject */
    protected ApiConfig $config;

    /** @var VersionTransformer */
    protected VersionTransformer $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->sut = new VersionTransformer($this->config);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::transform
     */
    public function testTransform(): void
    {
        $version = '__dummy_version__';
        $content = (object) ['info' => (object) ['version' => null]];

        $this->config->expects($this->once())->method('getAppVersion')->willReturn($version);

        $this->sut->transform($content);

        $this->assertEquals($version, $content->info->version);
    }
}
