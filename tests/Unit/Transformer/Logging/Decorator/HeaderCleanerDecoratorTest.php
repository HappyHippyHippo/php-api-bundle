<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging\Decorator;

use Hippy\Api\Transformer\Logging\Decorator\HeaderCleanerDecorator;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Decorator\HeaderCleanerDecorator */
class HeaderCleanerDecoratorTest extends TestCase
{
    /** @var array<string, array<string, mixed>> */
    private const DATA = [
        'request' => [
            'headers' => [
                'header1' => ['__dummy_header_content__'],
                'header2' => ['__dummy_header_content__'],
                'Authorization' => ['__dummy_authorization__'],
            ],
            'body' => [
                'field1' => ['__dummy_field_content__'],
                'field2' => ['__dummy_field_content__'],
            ],
        ],
        'response' => [
            'headers' => [
                'header1' => ['__dummy_header_content__'],
                'header2' => ['__dummy_header_content__'],
                'Authorization' => ['__dummy_authorization__'],
            ],
            'body' => [
                'field1' => ['__dummy_field_content__'],
                'field2' => ['__dummy_field_content__'],
            ],
        ],
        'other' => [
            'headers' => [
                'header1' => ['__dummy_header_content__'],
                'header2' => ['__dummy_header_content__'],
                'Authorization' => ['__dummy_authorization__'],
            ],
            'body' => [
                'field1' => ['__dummy_field_content__'],
                'field2' => ['__dummy_field_content__'],
            ],
        ],
    ];

    /**
     * @return void
     * @covers ::request
     * @covers ::cleanHeaders
     */
    public function testRequest(): void
    {
        $expected = [
            'request' => [
                'headers' => [
                    'header1' => '__dummy_header_content__',
                    'header2' => '__dummy_header_content__',
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__'],
                ],
            ],
            'response' => [
                'headers' => [
                    'header1' => ['__dummy_header_content__'],
                    'header2' => ['__dummy_header_content__'],
                    'Authorization' => ['__dummy_authorization__'],
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__'],
                ],
            ],
            'other' => [
                'headers' => [
                    'header1' => ['__dummy_header_content__'],
                    'header2' => ['__dummy_header_content__'],
                    'Authorization' => ['__dummy_authorization__'],
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__']
                ],
            ],
        ];

        $request = $this->createMock(Request::class);

        $sut = new HeaderCleanerDecorator();
        $this->assertEquals($expected, $sut->request(self::DATA, $request));
    }

    /**
     * @return void
     * @covers ::response
     * @covers ::cleanHeaders
     */
    public function testResponse(): void
    {
        $expected = [
            'request' => [
                'headers' => [
                    'header1' => '__dummy_header_content__',
                    'header2' => '__dummy_header_content__',
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__'],
                ],
            ],
            'response' => [
                'headers' => [
                    'header1' => '__dummy_header_content__',
                    'header2' => '__dummy_header_content__',
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__'],
                ],
            ],
            'other' => [
                'headers' => [
                    'header1' => ['__dummy_header_content__'],
                    'header2' => ['__dummy_header_content__'],
                    'Authorization' => ['__dummy_authorization__'],
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__']
                ],
            ],
        ];

        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $sut = new HeaderCleanerDecorator();
        $this->assertEquals($expected, $sut->response(self::DATA, $request, $response));
    }

    /**
     * @return void
     * @covers ::exception
     * @covers ::cleanHeaders
     */
    public function testException(): void
    {
        $expected = [
            'request' => [
                'headers' => [
                    'header1' => '__dummy_header_content__',
                    'header2' => '__dummy_header_content__',
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__'],
                ],
            ],
            'response' => [
                'headers' => [
                    'header1' => ['__dummy_header_content__'],
                    'header2' => ['__dummy_header_content__'],
                    'Authorization' => ['__dummy_authorization__'],
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__'],
                ],
            ],
            'other' => [
                'headers' => [
                    'header1' => ['__dummy_header_content__'],
                    'header2' => ['__dummy_header_content__'],
                    'Authorization' => ['__dummy_authorization__'],
                ],
                'body' => [
                    'field1' => ['__dummy_field_content__'],
                    'field2' => ['__dummy_field_content__']
                ],
            ],
        ];

        $request = $this->createMock(Request::class);
        $exception = new Exception();

        $sut = new HeaderCleanerDecorator();
        $this->assertEquals($expected, $sut->exception(self::DATA, $request, $exception));
    }
}
