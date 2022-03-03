<?php

namespace Hippy\Api\Tests\Unit\Listener;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Listener\LoggingEventSubscriber;
use Hippy\Api\Transformer\Logging\TransformerInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

/** @coversDefaultClass \Hippy\Api\Listener\LoggingEventSubscriber */
class LoggingEventSubscriberTest extends TestCase
{
    use EventCreatorTrait;

    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var TransformerInterface&MockObject */
    private TransformerInterface $transformer;

    /** @var LoggerInterface&MockObject */
    private LoggerInterface $logger;

    /** @var LoggingEventSubscriber */
    private LoggingEventSubscriber $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->transformer = $this->createMock(TransformerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->sut = new LoggingEventSubscriber($this->config, $this->transformer, $this->logger);
    }

    /**
     * @return void
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals([
            KernelEvents::REQUEST => [['onKernelRequest', 0]],
            KernelEvents::RESPONSE => [['onKernelResponse', 0]],
            KernelEvents::EXCEPTION => [['onKernelException', 0]],
        ], LoggingEventSubscriber::getSubscribedEvents());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestWhenNotActive(): void
    {
        $this->config->expects($this->once())->method('isLogRequestEnabled')->willReturn(false);
        $this->config->expects($this->never())->method('getLogRequestLevel');

        $event = $this->createRequestEvent();

        $this->sut->onKernelRequest($event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $level = 'debug';
        $message = '__dummy_message__';
        $context = ['__dummy_context__'];

        $this->config->expects($this->once())->method('isLogRequestEnabled')->willReturn(true);
        $this->config->expects($this->once())->method('getLogRequestLevel')->willReturn($level);
        $this->config->expects($this->once())->method('getLogRequestMessage')->willReturn($message);

        $request = $this->createMock(Request::class);
        $this->transformer
            ->expects($this->once())
            ->method('request')
            ->with($request)
            ->willReturn($context);
        $this->logger->expects($this->once())->method($level)->with($message, $context);

        $event = $this->createRequestEvent($request);

        $this->sut->onKernelRequest($event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelResponse
     */
    public function testOnKernelResponseWhenNotActive(): void
    {
        $this->config->expects($this->once())->method('isLogResponseEnabled')->willReturn(false);
        $this->config->expects($this->never())->method('getLogResponseLevel');

        $event = $this->createResponseEvent();

        $this->sut->onKernelResponse($event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelResponse
     */
    public function testOnKernelResponse(): void
    {
        $level = 'debug';
        $message = '__dummy_message__';
        $context = ['__dummy_context__'];

        $this->config->expects($this->once())->method('isLogResponseEnabled')->willReturn(true);
        $this->config->expects($this->once())->method('getLogResponseLevel')->willReturn($level);
        $this->config->expects($this->once())->method('getLogResponseMessage')->willReturn($message);

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $this->transformer
            ->expects($this->once())
            ->method('response')
            ->with($request, $response)
            ->willReturn($context);
        $this->logger->expects($this->once())->method($level)->with($message, $context);

        $event = $this->createResponseEvent($request, $response);

        $this->sut->onKernelResponse($event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelException
     */
    public function testOnKernelExceptionWhenNotActive(): void
    {
        $this->config->expects($this->once())->method('isLogExceptionEnabled')->willReturn(false);
        $this->config->expects($this->never())->method('getLogExceptionLevel');

        $event = $this->createExceptionEvent(new Exception());

        $this->sut->onKernelException($event);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::onKernelException
     */
    public function testOnKernelException(): void
    {
        $level = 'debug';
        $message = '__dummy_message__';
        $context = ['__dummy_context__'];

        $this->config->expects($this->once())->method('isLogExceptionEnabled')->willReturn(true);
        $this->config->expects($this->once())->method('getLogExceptionLevel')->willReturn($level);
        $this->config->expects($this->once())->method('getLogExceptionMessage')->willReturn($message);

        $request = $this->createMock(Request::class);
        $exception = new Exception();
        $this->transformer
            ->expects($this->once())
            ->method('exception')
            ->with($request, $exception)
            ->willReturn($context);
        $this->logger->expects($this->once())->method($level)->with($message, $context);

        $event = $this->createExceptionEvent($exception, $request);

        $this->sut->onKernelException($event);
    }
}
