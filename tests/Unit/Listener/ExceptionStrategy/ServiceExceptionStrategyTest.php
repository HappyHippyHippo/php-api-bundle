<?php

namespace Hippy\Api\Tests\Unit\Listener\ExceptionStrategy;

use Hippy\Api\Listener\ExceptionStrategy\ServiceExceptionStrategy;
use Hippy\Api\Tests\Unit\Listener\EventCreatorTrait;
use Hippy\Error\Error;
use Hippy\Exception\Exception as ServiceException;
use Hippy\Model\Model;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Listener\ExceptionStrategy\ServiceExceptionStrategy */
class ServiceExceptionStrategyTest extends TestCase
{
    use EventCreatorTrait;

    /** @var ServiceExceptionStrategy  */
    private ServiceExceptionStrategy $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->sut = new ServiceExceptionStrategy();
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupportReturnFalseIfNotServiceException(): void
    {
        $event = $this->createExceptionEvent(new Exception());
        $this->assertFalse($this->sut->supports($event));
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupportReturnTrueIfIsServiceException(): void
    {
        $event = $this->createExceptionEvent(new ServiceException());
        $this->assertTrue($this->sut->supports($event));
    }

    /**
     * @return void
     * @covers ::handle
     */
    public function testHandleWithoutDataOnException(): void
    {
        $statusCode = Response::HTTP_NOT_FOUND;
        $code = 123;
        $message = '__dummy_message__';
        $error = new Error($code, $message);
        $expected = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 'c123',
                    'message' => $message
                ]],
            ],
        ];

        $exception = new ServiceException($statusCode);
        $exception->addError($error);

        $event = $this->createExceptionEvent($exception);
        $this->sut->handle($event);

        $response = $event->getResponse();
        if (is_null($response)) {
            $this->fail('handler did store a valid response');
        }
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());

        $this->assertEquals($expected, json_decode((string) $response->getContent(), true));
    }

    /**
     * @return void
     * @covers ::handle
     */
    public function testHandleWithDataOnException(): void
    {
        $statusCode = Response::HTTP_NOT_FOUND;
        $code = 123;
        $message = '__dummy_message__';
        $error = new Error($code, $message);
        $data = ['__dummy_data__'];
        $expected = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 'c123',
                    'message' => $message
                ]],
            ],
            'data' => $data
        ];

        $model = $this->createMock(Model::class);
        $model->expects($this->once())->method('jsonSerialize')->willReturn($data);

        $exception = new ServiceException($statusCode);
        $exception->addError($error);
        $exception->setData($model);

        $event = $this->createExceptionEvent($exception);
        $this->sut->handle($event);

        $response = $event->getResponse();
        if (is_null($response)) {
            $this->fail('handler did store a valid response');
        }
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());

        $this->assertEquals($expected, json_decode((string) $response->getContent(), true));
    }
}
