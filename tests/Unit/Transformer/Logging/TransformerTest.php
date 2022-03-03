<?php

namespace Hippy\Api\Tests\Unit\Transformer\Logging;

use Hippy\Api\Transformer\Logging\Strategy\StrategyInterface;
use Hippy\Api\Transformer\Logging\Transformer;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/** @coversDefaultClass \Hippy\Api\Transformer\Logging\Transformer */
class TransformerTest extends TestCase
{
    private const REQUEST_URI = '__dummy_request_uri__';
    private const REQUEST_METHOD = '__dummy_request_method__';
    private const REQUEST_CLIENT_IP = '__dummy_request_client_ip__';
    private const REQUEST_HEADERS = ['--dummy-request-header-field--' => '__dummy_request_header_value__'];
    private const REQUEST_QUERY = ['__dummy_request_query_field__' => '__dummy_request_query_value__'];
    private const REQUEST_REQUEST = ['__dummy_request_request_field__' => '__dummy_request_request_value__'];
    private const REQUEST_ATTRIBUTES = ['__dummy_request_attribute_field__' => '__dummy_request_attribute_value__'];
    private const REQUEST = [
        'request' => [
            'uri' => self::REQUEST_URI,
            'method' => self::REQUEST_METHOD,
            'clientIp' => self::REQUEST_CLIENT_IP,
            'headers' => ['--dummy-request-header-field--' => ['__dummy_request_header_value__']],
            'query' => self::REQUEST_QUERY,
            'request' => self::REQUEST_REQUEST,
            'attributes' => self::REQUEST_ATTRIBUTES,
        ],
    ];

    private const RESPONSE_STATUS_CODE = 123;
    private const RESPONSE_HEADERS = ['--dummy-response-header-field--' => '__dummy_response_header_value__'];
    private const RESPONSE = [
        'response' => [
            'status' => self::RESPONSE_STATUS_CODE,
            'headers' => ['--dummy-response-header-field--' => ['__dummy_response_header_value__']],
        ],
    ];

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $strategy1 = $this->createMock(StrategyInterface::class);
        $strategy1->method('priority')->willReturn(2);
        $strategy2 = $this->createMock(StrategyInterface::class);
        $strategy2->method('priority')->willReturn(3);
        $strategy3 = $this->createMock(StrategyInterface::class);
        $strategy3->method('priority')->willReturn(1);

        $sut = new Transformer([$strategy1, $strategy2, $strategy3]);

        $prop = new ReflectionProperty(Transformer::class, 'strategies');
        $this->assertEquals([$strategy2, $strategy1, $strategy3], $prop->getValue($sut));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::request
     * @covers ::getRequestData
     */
    public function testRequest(): void
    {
        $eventRequest = $this->createRequest();

        $sut = new Transformer([]);
        $this->assertEquals(self::REQUEST, $sut->request($eventRequest));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::request
     * @covers ::getRequestData
     */
    public function testRequestReturnStrategyDecoratedData(): void
    {
        $baseData = self::REQUEST;
        $expected = array_merge($baseData, ['extra' => ['__dummy_decorated_data__']]);

        $eventRequest = $this->createRequest();

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy
            ->expects($this->once())
            ->method('supports')
            ->with($eventRequest)
            ->willReturn(true);
        $strategy
            ->expects($this->once())
            ->method('request')
            ->with($baseData, $eventRequest)
            ->willReturn($expected);

        $sut = new Transformer([$strategy]);
        $this->assertEquals($expected, $sut->request($eventRequest));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::response
     * @covers ::getRequestData
     * @covers ::getResponseData
     */
    public function testResponse(): void
    {
        $eventRequest = $this->createRequest();
        $eventResponse = $this->createResponse();
        $expected = array_merge(self::REQUEST, self::RESPONSE);
        $expected['response']['headers'] = array_merge(
            $expected['response']['headers'],
            $eventResponse->headers->all()
        );

        $sut = new Transformer([]);
        $response = $sut->response($eventRequest, $eventResponse);

        $this->assertEquals($expected, $response);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::response
     * @covers ::getRequestData
     * @covers ::getResponseData
     */
    public function testResponseReturnStrategyDecoratedData(): void
    {
        $eventRequest = $this->createRequest();
        $eventResponse = $this->createResponse();
        $baseData = array_merge(self::REQUEST, self::RESPONSE);
        $baseData['response']['headers'] = array_merge(
            $baseData['response']['headers'],
            $eventResponse->headers->all()
        );
        $expected = array_merge($baseData, ['extra' => ['__dummy_decorated_data__']]);

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy
            ->expects($this->once())
            ->method('supports')
            ->with($eventRequest)
            ->willReturn(true);
        $strategy
            ->expects($this->once())
            ->method('response')
            ->with($baseData, $eventRequest, $eventResponse)
            ->willReturn($expected);

        $sut = new Transformer([$strategy]);
        $response = $sut->response($eventRequest, $eventResponse);

        $expected['response']['headers']['date'] = $response['response']['headers']['date'];
        $expected['response']['headers']['cache-control'] = $response['response']['headers']['cache-control'];

        $this->assertEquals($expected, $response);
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::exception
     * @covers ::getRequestData
     * @covers ::getExceptionData
     */
    public function testException(): void
    {
        $exCode = 123;
        $exMessage = '__dummy_message__';
        $eventResponse = new Exception($exMessage, $exCode);

        $eventRequest = $this->createRequest();

        $sut = new Transformer([]);
        $this->assertEquals(
            array_merge(
                self::REQUEST,
                [
                    'exception' => [
                        'message' => $exMessage,
                        'file' => __FILE__,
                        'line' => $eventResponse->getLine(),
                        'trace' => $eventResponse->getTrace(),
                    ],
                ]
            ),
            $sut->exception($eventRequest, $eventResponse)
        );
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::exception
     * @covers ::getRequestData
     * @covers ::getExceptionData
     */
    public function testExceptionReturnStrategyDecoratedData(): void
    {
        $exCode = 123;
        $exMessage = '__dummy_message__';
        $eventResponse = new Exception($exMessage, $exCode);

        $baseData = array_merge(
            self::REQUEST,
            [
                'exception' => [
                    'message' => $exMessage,
                    'file' => __FILE__,
                    'line' => $eventResponse->getLine(),
                    'trace' => $eventResponse->getTrace(),
                ],
            ]
        );
        $expected = array_merge($baseData, ['extra' => ['__dummy_decorated_data__']]);

        $eventRequest = $this->createRequest();

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy
            ->expects($this->once())
            ->method('supports')
            ->with($eventRequest)
            ->willReturn(true);
        $strategy
            ->expects($this->once())
            ->method('exception')
            ->with($baseData, $eventRequest, $eventResponse)
            ->willReturn($expected);

        $sut = new Transformer([$strategy]);

        $this->assertEquals($expected, $sut->exception($eventRequest, $eventResponse));
    }

    /**
     * @return Request
     */
    protected function createRequest(): Request
    {
        $mock = $this->createMock(Request::class);
        $mock->expects($this->once())->method('getUri')->willReturn(self::REQUEST_URI);
        $mock->expects($this->once())->method('getMethod')->willReturn(self::REQUEST_METHOD);
        $mock->expects($this->once())->method('getClientIp')->willReturn(self::REQUEST_CLIENT_IP);

        $mock->headers = new HeaderBag(self::REQUEST_HEADERS);
        $mock->query = new InputBag(self::REQUEST_QUERY);
        $mock->request = new InputBag(self::REQUEST_REQUEST);
        $mock->attributes = new InputBag(self::REQUEST_ATTRIBUTES);

        return $mock;
    }

    /**
     * @return Response
     */
    protected function createResponse(): Response
    {
        $mock = $this->createMock(Response::class);
        $mock->expects($this->once())->method('getStatusCode')->willReturn(self::RESPONSE_STATUS_CODE);

        $mock->headers = new ResponseHeaderBag(self::RESPONSE_HEADERS);

        return $mock;
    }
}
