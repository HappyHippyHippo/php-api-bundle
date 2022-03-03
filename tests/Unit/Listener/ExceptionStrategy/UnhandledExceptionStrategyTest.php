<?php

namespace Hippy\Api\Tests\Unit\Listener\ExceptionStrategy;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Listener\ExceptionStrategy\UnhandledExceptionStrategy;
use Hippy\Api\Tests\Unit\Listener\EventCreatorTrait;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Listener\ExceptionStrategy\UnhandledExceptionStrategy */
class UnhandledExceptionStrategyTest extends TestCase
{
    use EventCreatorTrait;

    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var UnhandledExceptionStrategy */
    private UnhandledExceptionStrategy $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->sut = new UnhandledExceptionStrategy($this->config);
    }

    /**
     * @return void
     * @covers ::supports
     */
    public function testSupport(): void
    {
        $event = $this->createExceptionEvent(new Exception());
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
        $code = 123;
        $message = '__dummy_message__';
        $exception = new Exception($message, $code);
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
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $this->assertEquals($expected, json_decode((string) $response->getContent(), true));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::handle
     * @covers ::createError
     */
    public function testHandleWithTrace(): void
    {
        $code = 123;
        $message = '__dummy_message__';
        $exception = new Exception($message, $code);

        $this->config->expects($this->once())->method('isErrorTraceEnabled')->willReturn(true);

        $event = $this->createExceptionEvent($exception);
        $this->sut->handle($event);

        $response = $event->getResponse();
        if (is_null($response)) {
            $this->fail('handler did store a valid response');
        }
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

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
