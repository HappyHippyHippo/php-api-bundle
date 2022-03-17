<?php

namespace Hippy\Api\Tests\Unit\Service\Base;

use Hippy\Api\Service\Base\ConfigService;
use Hippy\Config\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Service\Base\ConfigService */
class ConfigServiceTest extends TestCase
{
    /** @var Config&MockObject */
    private Config $config;

    /** @var ConfigService */
    private ConfigService $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->sut = new ConfigService($this->config);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::process
     */
    public function testProcess(): void
    {
        $response = $this->sut->process();

        $this->assertSame($this->config, $response->getConfig());
    }
}
