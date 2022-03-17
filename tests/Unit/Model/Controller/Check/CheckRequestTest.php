<?php

namespace Hippy\Api\Tests\Unit\Model\Controller\Check;

use Hippy\Api\Model\Controller\Check\CheckRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

/** @coversDefaultClass \Hippy\Api\Model\Controller\Check\CheckRequest */
class CheckRequestTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testIsDepth(): void
    {
        $request = $this->createMock(Request::class);
        $request->query = new InputBag(['deep' => true]);

        $sut = new CheckRequest($request);

        $this->assertEquals(true, $sut->isDeep());
    }
}
