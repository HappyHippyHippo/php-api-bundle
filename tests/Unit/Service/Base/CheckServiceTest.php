<?php

namespace Hippy\Api\Tests\Unit\Service\Base;

use Hippy\Api\Model\Controller\Check\CheckRequest;
use Hippy\Api\Model\Controller\Check\CheckResponse;
use Hippy\Api\Service\Base\Check\CheckInterface;
use Hippy\Api\Service\Base\CheckService;
use Hippy\Exception\Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Api\Service\Base\CheckService */
class CheckServiceTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructThrowsOnInvalidCheckInstance(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CheckService([123]); // @phpstan-ignore-line
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructStoresTheCheckInstances(): void
    {
        $check1 = $this->createMock(CheckInterface::class);
        $check2 = $this->createMock(CheckInterface::class);

        $sut = new CheckService([$check1, $check2]);

        $prop = new ReflectionProperty(CheckService::class, 'checks');
        $this->assertEquals([$check1, $check2], $prop->getValue($sut));
    }

    /**
     * @return void
     * @covers ::check
     */
    public function testCheckThrowsOnFailedCheck(): void
    {
        $this->expectException(Exception::class);

        $check1 = $this->createMock(CheckInterface::class);
        $check1
            ->expects($this->once())
            ->method('deepCheck')
            ->with($this->isInstanceOf(CheckResponse::class))
            ->willReturn(false);
        $check2 = $this->createMock(CheckInterface::class);
        $check2
            ->expects($this->once())
            ->method('deepCheck')
            ->with($this->isInstanceOf(CheckResponse::class))
            ->willReturn(true);

        $request = $this->createMock(CheckRequest::class);
        $request->expects($this->once())->method('isDeep')->willReturn(true);

        $sut = new CheckService([$check1, $check2]);
        $sut->check($request);
    }

    /**
     * @return void
     * @covers ::check
     */
    public function testCheckReturnCheckResponseOnSuccess(): void
    {
        $check1 = $this->createMock(CheckInterface::class);
        $check1
            ->expects($this->once())
            ->method('deepCheck')
            ->with($this->isInstanceOf(CheckResponse::class))
            ->willReturn(true);
        $check2 = $this->createMock(CheckInterface::class);
        $check2
            ->expects($this->once())
            ->method('deepCheck')
            ->with($this->isInstanceOf(CheckResponse::class))
            ->willReturn(true);

        $request = $this->createMock(CheckRequest::class);
        $request->expects($this->once())->method('isDeep')->willReturn(true);

        $sut = new CheckService([$check1, $check2]);
        $this->assertInstanceOf(CheckResponse::class, $sut->check($request));
    }

    /**
     * @return void
     * @covers ::check
     */
    public function testCheckCallShallowCheckMethodIfNotDeepRequest(): void
    {
        $check1 = $this->createMock(CheckInterface::class);
        $check1
            ->expects($this->once())
            ->method('shallowCheck')
            ->with($this->isInstanceOf(CheckResponse::class))
            ->willReturn(true);
        $check2 = $this->createMock(CheckInterface::class);
        $check2
            ->expects($this->once())
            ->method('shallowCheck')
            ->with($this->isInstanceOf(CheckResponse::class))
            ->willReturn(true);

        $request = $this->createMock(CheckRequest::class);
        $request->expects($this->once())->method('isDeep')->willReturn(false);

        $sut = new CheckService([$check1, $check2]);
        $this->assertInstanceOf(CheckResponse::class, $sut->check($request));
    }
}
