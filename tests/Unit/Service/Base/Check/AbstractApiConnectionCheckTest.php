<?php

namespace Hippy\Api\Tests\Unit\Service\Base\Check;

use Hippy\Api\Model\Controller\Check\CheckResponse;
use Hippy\Api\Service\Base\Check\AbstractApiConnectionCheck;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Service\Base\Check\AbstractApiConnectionCheck */
class AbstractApiConnectionCheckTest extends TestCase
{
    /** @var string */
    protected const NAME = '__dummy_name__';

    /** @var CheckResponse&MockObject */
    protected CheckResponse $response;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->response = $this->createMock(CheckResponse::class);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::deepCheck
     */
    public function testDeepCheckFailsOnConnectionException(): void
    {
        $message = '__dummy_error_message__';
        $exception = new Exception($message);

        $callback = function () use ($exception) {
            throw $exception;
        };

        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, false, 'api connection returned non-200 status code')
            ->willReturn($this->response);

        $sut = $this->getMockForAbstractClass(AbstractApiConnectionCheck::class, [self::NAME, $callback]);
        $this->assertFalse($sut->deepCheck($this->response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::deepCheck
     */
    public function testDeepCheckSuccess(): void
    {
        $callback = function () {
        };

        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, true, 'api connection checked successfully', [])
            ->willReturn($this->response);

        $sut = $this->getMockForAbstractClass(AbstractApiConnectionCheck::class, [self::NAME, $callback]);
        $this->assertTrue($sut->deepCheck($this->response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::shallowCheck
     */
    public function testShallowCheckSuccess(): void
    {
        $callback = function () {
        };

        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, true, 'api connection not tested (shallow check)')
            ->willReturn($this->response);

        $sut = $this->getMockForAbstractClass(AbstractApiConnectionCheck::class, [self::NAME, $callback]);
        $this->assertTrue($sut->shallowCheck($this->response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::deepCheck
     */
    public function testAddResultingDeepCheckExtraData(): void
    {
        $data = ['field' => '__dummy_data__'];
        $callback = function () use ($data) {
            return $data;
        };

        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, true, 'api connection checked successfully', $data)
            ->willReturn($this->response);

        $sut = $this->getMockForAbstractClass(AbstractApiConnectionCheck::class, [self::NAME, $callback]);
        $this->assertTrue($sut->deepCheck($this->response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::deepCheck
     */
    public function testPreventDeepCheckNonArrayResultingData(): void
    {
        $callback = function () {
            return '__dummy_string__';
        };

        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, true, 'api connection checked successfully', [])
            ->willReturn($this->response);

        $sut = $this->getMockForAbstractClass(AbstractApiConnectionCheck::class, [self::NAME, $callback]);
        $this->assertTrue($sut->deepCheck($this->response));
    }
}
