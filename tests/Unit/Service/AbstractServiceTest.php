<?php

namespace Hippy\Api\Tests\Unit\Service;

use Hippy\Api\Error\ErrorCode;
use Hippy\Api\Service\AbstractService;
use Hippy\Error\ErrorCode as ErrorCodeAlias;
use Hippy\Exception\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Service\AbstractService */
class AbstractServiceTest extends TestCase
{
    /** @var AbstractService&MockObject */
    private AbstractService $sut;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->sut = $this->getMockForAbstractClass(AbstractService::class);
    }

    /**
     * @return void
     * @covers ::throws
     * @throws ReflectionException
     */
    public function testThrowsUsesDefaultMessageConverterIfNoMessageIsGiven(): void
    {
        $status = 123;
        $error = ErrorCode::MALFORMED_JSON;
        $expected = [
            [
                'code' => 'c' . $error,
                'message' => ErrorCode::ERROR_TO_MESSAGE[$error]
            ],
        ];

        $method = new ReflectionMethod(AbstractService::class, 'throws');

        try {
            $method->invoke($this->sut, $status, $error);
        } catch (Exception $exception) { // @phpstan-ignore-line
            $this->assertEquals($status, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }

        $this->fail('did not throw');
    }

    /**
     * @return void
     * @covers ::throws
     * @throws ReflectionException
     */
    public function testThrowsUsesGivenMessage(): void
    {
        $status = 123;
        $error = ErrorCode::MALFORMED_JSON;
        $message = '__dummy_message__';
        $expected = [
            [
                'code' => 'c' . $error,
                'message' => $message
            ],
        ];

        $method = new ReflectionMethod(AbstractService::class, 'throws');

        try {
            $method->invoke($this->sut, $status, $error, $message);
        } catch (Exception $exception) { // @phpstan-ignore-line
            $this->assertEquals($status, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }

        $this->fail('did not throw');
    }

    /**
     * @return void
     * @covers ::throwsUnknown
     * @throws ReflectionException
     */
    public function testThrowsUnknown(): void
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $error = ErrorCodeAlias::UNKNOWN;
        $expected = [
            [
                'code' => 'c' . $error,
                'message' => ErrorCode::ERROR_TO_MESSAGE[$error]
            ],
        ];

        $method = new ReflectionMethod(AbstractService::class, 'throwsUnknown');

        try {
            $method->invoke($this->sut);
        } catch (Exception $exception) { // @phpstan-ignore-line
            $this->assertEquals($status, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }

        $this->fail('did not throw');
    }

    /**
     * @param array<int, int> $errors
     * @param array<int, string>|null $messages
     * @param array<int, mixed> $expected
     * @return void
     * @covers ::throwsMany
     * @dataProvider providerForThrowsManyTest
     * @throws ReflectionException
     */
    public function testThrowsMany(array $errors, ?array $messages, array $expected): void
    {
        $status = 123;

        $method = new ReflectionMethod(AbstractService::class, 'throwsMany');

        try {
            $method->invoke($this->sut, $status, $errors, $messages);
        } catch (Exception $exception) { // @phpstan-ignore-line
            $this->assertEquals($status, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }

        $this->fail('did not throw');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForThrowsManyTest(): array
    {
        return [
            'without messages' => [
                'errors' => [ErrorCode::MALFORMED_JSON, ErrorCode::NOT_ENABLED],
                'messages' => null,
                'expected' => [
                    [
                        'code' => 'c' . ErrorCode::MALFORMED_JSON,
                        'message' => ErrorCode::ERROR_TO_MESSAGE[ErrorCode::MALFORMED_JSON]
                    ],
                    [
                        'code' => 'c' . ErrorCode::NOT_ENABLED,
                        'message' => ErrorCode::ERROR_TO_MESSAGE[ErrorCode::NOT_ENABLED]
                    ]
                ]
            ],
            'with messages' => [
                'errors' => [ErrorCode::MALFORMED_JSON, ErrorCode::NOT_ENABLED],
                'messages' => [
                    ErrorCode::MALFORMED_JSON => '__dummy_message__'
                ],
                'expected' => [
                    [
                        'code' => 'c' . ErrorCode::MALFORMED_JSON,
                        'message' => '__dummy_message__'
                    ],
                    [
                        'code' => 'c' . ErrorCode::NOT_ENABLED,
                        'message' => ErrorCode::ERROR_TO_MESSAGE[ErrorCode::NOT_ENABLED]
                    ]
                ]
            ]
        ];
    }
}
