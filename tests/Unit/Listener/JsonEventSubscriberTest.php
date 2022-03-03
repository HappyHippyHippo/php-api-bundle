<?php

namespace Hippy\Api\Tests\Unit\Listener;

use Hippy\Api\Error\ErrorCode;
use Hippy\Api\Listener\JsonEventSubscriber;
use Hippy\Error\Error;
use Hippy\Exception\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/** @coversDefaultClass \Hippy\Api\Listener\JsonEventSubscriber */
class JsonEventSubscriberTest extends TestCase
{
    /** @var JsonEventSubscriber  */
    private JsonEventSubscriber $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->sut = new JsonEventSubscriber();
    }

    /**
     * @return void
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals([
            KernelEvents::REQUEST => [['onKernelRequest', 10]],
        ], JsonEventSubscriber::getSubscribedEvents());
    }

    /**
     * @return void
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestIgnoreWhenNotHavingContentTypeHeader(): void
    {
        $request = $this->createMock(Request::class);
        $request->headers = new HeaderBag();
        $request->request = new InputBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->sut->onKernelRequest(new RequestEvent($kernel, $request, $requestType));

        $this->assertEmpty($request->request);
    }

    /**
     * @return void
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestIgnoreOnNonJsonContentType(): void
    {
        $request = $this->createMock(Request::class);
        $request->headers = new HeaderBag(['Content-Type' => 'text/html']);
        $request->request = new InputBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->sut->onKernelRequest(new RequestEvent($kernel, $request, $requestType));

        $this->assertEmpty($request->request);
    }

    /**
     * @return void
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestThrowOnInvalidJson(): void
    {
        $expected = [
            [
                'code' => 'c' . ErrorCode::MALFORMED_JSON,
                'message' => ErrorCode::ERROR_TO_MESSAGE[ErrorCode::MALFORMED_JSON],
            ],
        ];

        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getContent')->willReturn('{');
        $request->headers = new HeaderBag(['Content-Type' => 'application/json']);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        try {
            $this->sut->onKernelRequest(new RequestEvent($kernel, $request, $requestType));
        } catch (Exception $exception) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestThrowIfInvalidJson(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getContent')->willReturn('{"field:value}');
        $request->headers = new HeaderBag(['Content-Type' => 'application/json']);
        $request->request = new InputBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->expectExceptionObject((new Exception(Response::HTTP_BAD_REQUEST))->addError(
            new Error(ErrorCode::MALFORMED_JSON, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::MALFORMED_JSON])
        ));

        $this->sut->onKernelRequest(new RequestEvent($kernel, $request, $requestType));
    }

    /**
     * @return void
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestThrowOnNonObjectJson(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getContent')->willReturn('string');
        $request->headers = new HeaderBag(['Content-Type' => 'application/json']);
        $request->request = new InputBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->expectExceptionObject((new Exception(Response::HTTP_BAD_REQUEST))->addError(
            new Error(ErrorCode::MALFORMED_JSON, ErrorCode::ERROR_TO_MESSAGE[ErrorCode::MALFORMED_JSON])
        ));

        $this->sut->onKernelRequest(new RequestEvent($kernel, $request, $requestType));
    }

    /**
     * @return void
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestStorePostValues(): void
    {
        $expected = ['field' => '__dummy_value__'];
        $json = json_encode($expected);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getContent')->willReturn($json);
        $request->headers = new HeaderBag(['Content-Type' => 'application/json']);
        $request->request = new InputBag();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $requestType = 0;

        $this->sut->onKernelRequest(new RequestEvent($kernel, $request, $requestType));

        $this->assertEquals($expected, $request->request->all());
    }
}
