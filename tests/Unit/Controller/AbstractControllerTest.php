<?php

namespace Hippy\Api\Tests\Unit\Controller;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Controller\AbstractController;
use Hippy\Error\Error;
use Hippy\Exception\Exception;
use Hippy\Exception\RedirectException;
use Hippy\Model\Model;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Controller\AbstractController */
class AbstractControllerTest extends TestCase
{
    /** @var int */
    private int $service;

    /** @var string */
    private string $endpoint;

    /** @var ApiConfigInterface&MockObject */
    private ApiConfigInterface $config;

    /** @var AbstractController&MockObject */
    private AbstractController $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->service = 123;
        $this->endpoint = '__dummy_endpoint__';

        $this->config = $this->createMock(ApiConfigInterface::class);
        $this->config->method('getAppId')->willReturn($this->service);

        $this->sut = $this->getMockForAbstractClass(
            AbstractController::class,
            [$this->config, $this->endpoint],
            '',
            true,
            true,
            true,
            ['json']
        );

        $this->sut
            ->method('json')
            ->willReturnCallback(function ($data, int $status = 200, array $headers = []) {
                return new JsonResponse($data, $status, $headers);
            });
    }

    /**
     * @return void
     * @covers ::isEnabled
     * @throws ReflectionException
     */
    public function testIsEnabled(): void
    {
        $invoker = new ReflectionMethod(AbstractController::class, 'isEnabled');
        $this->assertTrue($invoker->invoke($this->sut));
    }

    /**
     * @return void
     * @covers ::setEndpointCode
     * @throws ReflectionException
     */
    public function testSetEndpointCode(): void
    {
        $code = '__dummy_endpoint_code__';

        $invoker = new ReflectionMethod(AbstractController::class, 'setEndpointCode');
        $this->assertSame($this->sut, $invoker->invoke($this->sut, $code));

        $prop = new ReflectionProperty(AbstractController::class, 'endpointCode');
        $this->assertEquals($code, $prop->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::envelope
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testEnvelopeAddServiceAndEndpointValueOnExceptionErrors(): void
    {
        $errorGen = function (): Error {
            $error = $this->createMock(Error::class);
            $error
                ->expects($this->once())
                ->method('setService')
                ->with($this->service)
                ->willReturn($error);
            $error
                ->expects($this->once())
                ->method('setEndpoint')
                ->with($this->endpoint)
                ->willReturn($error);

            return $error;
        };

        $exception = new Exception();
        $exception->addError($errorGen());
        $exception->addError($errorGen());
        $exception->addError($errorGen());

        $this->expectExceptionObject($exception);

        $this->invokeEnvelope(function () use ($exception) {
            throw $exception;
        });
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::envelope
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testEnvelopeRedirectOnRedirectException(): void
    {
        $url = '__dummy_url__';
        $exception = new RedirectException($url);

        /** @var RedirectResponse $response */
        $response = $this->invokeEnvelope(function () use ($exception) {
            throw $exception;
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($url, $response->getTargetUrl());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::envelope
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testEnvelopeThrowsIfConnectorIsDisabled(): void
    {
        $expected = [
            ['code' => 's123.__dummy_endpoint__.c2', 'message' => 'Not enabled'],
        ];

        $this->sut = $this->getMockForAbstractClass(
            AbstractController::class,
            [$this->config, $this->endpoint],
            '',
            true,
            true,
            true,
            ['json', 'isEnabled']
        );

        $this->sut->expects($this->once())->method('isEnabled')->willReturn(false);

        try {
            $this->invokeEnvelope(function () {
            });
        } catch (Exception $exception) { // @phpstan-ignore-line
            $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::envelope
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testEnvelope(): void
    {
        $json = ['__dummy_data__'];

        $model = $this->createMock(Model::class);
        $model->expects($this->once())->method('jsonSerialize')->willReturn($json);

        $result = $this->invokeEnvelope(function () use ($model) {
            return $model;
        });

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals([
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => $json,
        ], json_decode((string) $result->getContent(), true));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::__construct
     * @covers ::envelope
     * @covers ::execute
     */
    public function testEnvelopeWithEmptyResponseContent(): void
    {
        $result = $this->invokeEnvelope(function () {
            return null;
        });

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals([
            'status' => [
                'success' => true,
                'errors' => [],
            ],
        ], json_decode((string) $result->getContent(), true));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::respond
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testRespondAddServiceAndEndpointValueOnExceptionErrors(): void
    {
        $errorGen = function (): Error {
            $error = $this->createMock(Error::class);
            $error
                ->expects($this->once())
                ->method('setService')
                ->with($this->service)
                ->willReturn($error);
            $error
                ->expects($this->once())
                ->method('setEndpoint')
                ->with($this->endpoint)
                ->willReturn($error);

            return $error;
        };

        $exception = new Exception();
        $exception->addError($errorGen());
        $exception->addError($errorGen());
        $exception->addError($errorGen());

        $this->expectExceptionObject($exception);

        $this->invokeRespond(function () use ($exception) {
            throw $exception;
        });
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::respond
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testRespondRedirectOnRedirectException(): void
    {
        $url = '__dummy_url__';
        $exception = new RedirectException($url);

        /** @var RedirectResponse $response */
        $response = $this->invokeRespond(function () use ($exception) {
            throw $exception;
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($url, $response->getTargetUrl());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::respond
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testRespondThrowsIfConnectorIsDisabled(): void
    {
        $expected = [
            ['code' => 's123.__dummy_endpoint__.c2', 'message' => 'Not enabled'],
        ];

        $this->sut = $this->getMockForAbstractClass(
            AbstractController::class,
            [$this->config, $this->endpoint],
            '',
            true,
            true,
            true,
            ['json', 'isEnabled']
        );

        $this->sut->expects($this->once())->method('isEnabled')->willReturn(false);

        try {
            $this->invokeRespond(function () {
            });
        } catch (Exception $exception) { // @phpstan-ignore-line
            $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::respond
     * @covers ::execute
     * @throws ReflectionException
     */
    public function testRespond(): void
    {
        $content = '__dummy_data__';

        $result = $this->invokeRespond(function () use ($content) {
            return $content;
        });

        $this->assertEquals($content, $result->getContent());
    }

    /**
     * @param callable $func
     * @return Response
     * @throws ReflectionException
     */
    private function invokeEnvelope(callable $func): Response
    {
        $invoker = new ReflectionMethod(AbstractController::class, 'envelope');

        return $invoker->invoke($this->sut, $func);
    }

    /**
     * @param callable $func
     * @return Response
     * @throws ReflectionException
     */
    private function invokeRespond(callable $func): Response
    {
        $invoker = new ReflectionMethod(AbstractController::class, 'respond');

        return $invoker->invoke($this->sut, $func);
    }
}
