<?php

namespace Hippy\Api\Tests\Unit\Listener\ExceptionStrategy;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Listener\ExceptionStrategy\MethodNotAllowedExceptionStrategy;
use Hippy\Api\Tests\Unit\Listener\EventCreatorTrait;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/** @coversDefaultClass \Hippy\Api\Listener\ExceptionStrategy\MethodNotAllowedExceptionStrategy */
class MethodNotAllowedExceptionStrategyTest extends TestCase
{
    use EventCreatorTrait;

    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var MethodNotAllowedExceptionStrategy */
    private MethodNotAllowedExceptionStrategy $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->sut = new MethodNotAllowedExceptionStrategy($this->config);
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupportReturnFalseOnInvalidException(): void
    {
        $event = $this->createExceptionEvent(new Exception());
        $this->assertFalse($this->sut->supports($event));
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupportReturnTrueOnAcceptedException(): void
    {
        $event = $this->createExceptionEvent(new MethodNotAllowedException(['HEAD', 'GET']));
        $this->assertTrue($this->sut->supports($event));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::handle
     * @covers ::createError
     */
    public function testHandleWithoutTrace(): void
    {
        $code = 3;
        $message = '__dummy_message__';
        $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
        $exception = new MethodNotAllowedException(['HEAD', 'GET'], $message, 123, null);
        $expected = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 'c' . $code,
                    'message' => $message
                ]],
            ],
        ];

        $this->config->expects($this->once())->method('isErrorTraceEnabled')->willReturn(false);

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
     * @covers ::__construct
     * @covers ::handle
     * @covers ::createError
     */
    public function testHandleWithTrace(): void
    {
        $code = 3;
        $message = '__dummy_message__';
        $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
        $exception = new MethodNotAllowedException(['HEAD', 'GET'], $message, 123, null);

        $this->config->expects($this->once())->method('isErrorTraceEnabled')->willReturn(true);

        $event = $this->createExceptionEvent($exception);
        $this->sut->handle($event);

        $response = $event->getResponse();
        if (is_null($response)) {
            $this->fail('handler did store a valid response');
        }
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());

        $json = json_decode((string) $response->getContent(), true);

        if (
            !is_array($json)
            || !isset($json['status'])
            || !is_array($json['status'])
            || !isset($json['status']['errors'])
            || !is_array($json['status']['errors'])
            || !isset($json['status']['errors'][0])
        ) {
            $this->fail('invalid non-enveloped response');
        }

        $this->assertArrayHasKey('code', $json['status']['errors'][0]);
        $this->assertArrayHasKey('message', $json['status']['errors'][0]);
        $this->assertArrayHasKey('file', $json['status']['errors'][0]);
        $this->assertArrayHasKey('line', $json['status']['errors'][0]);
        $this->assertArrayHasKey('trace', $json['status']['errors'][0]);

        $this->assertEquals('c' . $code, $json['status']['errors'][0]['code']);
        $this->assertEquals($message, $json['status']['errors'][0]['message']);
        $this->assertEquals(__FILE__, $json['status']['errors'][0]['file']);
    }
}
