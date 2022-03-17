<?php

namespace Hippy\Api\Tests\Unit\Model\Controller\Config;

use Hippy\Api\Model\Controller\Config\ConfigResponse;
use Hippy\Config\Config;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Model\Controller\Config\ConfigResponse */
class ConfigResponseTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $config = $this->createMock(Config::class);

        $sut = new ConfigResponse($config);

        $this->assertSame($config, $sut->getConfig());
    }
}
