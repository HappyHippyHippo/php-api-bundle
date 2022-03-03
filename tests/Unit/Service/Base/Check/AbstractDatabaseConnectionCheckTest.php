<?php

namespace Hippy\Api\Tests\Unit\Service\Base\Check;

use Doctrine\DBAL\Connection;
use Hippy\Api\Model\Controller\Check\CheckResponse;
use Hippy\Api\Service\Base\Check\AbstractDatabaseConnectionCheck;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Service\Base\Check\AbstractDatabaseConnectionCheck */
class AbstractDatabaseConnectionCheckTest extends TestCase
{
    /** @var string */
    protected const NAME = '__dummy_name__';

    /** @var Connection&MockObject */
    protected Connection $connection;

    /** @var CheckResponse&MockObject */
    protected CheckResponse $response;

    /** @var AbstractDatabaseConnectionCheck&MockObject */
    protected AbstractDatabaseConnectionCheck $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->response = $this->createMock(CheckResponse::class);
        $this->sut = $this->getMockForAbstractClass(
            AbstractDatabaseConnectionCheck::class,
            [self::NAME, $this->connection]
        );
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

        $this->connection
            ->expects($this->once())
            ->method('connect')
            ->willThrowException($exception);
        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, false, $message)
            ->willReturn($this->response);

        $this->assertFalse($this->sut->deepCheck($this->response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::deepCheck
     */
    public function testDeepCheckSuccess(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('connect');
        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, true, 'connection established successfully')
            ->willReturn($this->response);

        $this->assertTrue($this->sut->deepCheck($this->response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::shallowCheck
     */
    public function testShallowCheckFailsOnConnectionException(): void
    {
        $message = '__dummy_error_message__';
        $exception = new Exception($message);

        $this->connection
            ->expects($this->once())
            ->method('connect')
            ->willThrowException($exception);
        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, false, $message)
            ->willReturn($this->response);

        $this->assertFalse($this->sut->shallowCheck($this->response));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::shallowCheck
     */
    public function testShallowCheckSuccess(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('connect');
        $this->response
            ->expects($this->once())
            ->method('addCheck')
            ->with(self::NAME, true, 'connection established successfully')
            ->willReturn($this->response);

        $this->assertTrue($this->sut->shallowCheck($this->response));
    }
}
