<?php

namespace Hippy\Api\Tests\Unit\Controller\Base;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Controller\Base\CheckController;
use Hippy\Api\Model\Controller\Check\CheckRequest;
use Hippy\Api\Model\Controller\Check\CheckResponse;
use Hippy\Api\Service\Base\CheckService;
use Hippy\Model\Envelope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Controller\Base\CheckController */
class CheckControllerTest extends TestCase
{
    /** @var ApiConfigInterface&MockObject  */
    private ApiConfigInterface $config;

    /** @var CheckService&MockObject */
    private CheckService $service;

    /** @var CheckController&MockObject */
    private CheckController $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfigInterface::class);
        $this->service = $this->createMock(CheckService::class);
        $this->sut = $this->getMockBuilder(CheckController::class)
            ->setConstructorArgs([$this->config, $this->service])
            ->onlyMethods(['json'])
            ->getMock();
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorStoreTheCorrectDomainCode(): void
    {
        $prop = new ReflectionProperty(CheckController::class, 'endpointCode');
        $this->assertEquals(2, $prop->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::preflight
     */
    public function testPreflight(): void
    {
        $origin = '__dummy_origin__';
        $expected = [
            'Access-Control-Allow-Methods' => 'HEAD, GET',
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Headers' => 'Content-Type',
        ];

        $this->config->expects($this->once())->method('getCorsOrigin')->willReturn($origin);

        $response = $this->sut->preflight();

        foreach ($expected as $header => $value) {
            $this->assertTrue($response->headers->has($header));
            $this->assertEquals($value, $response->headers->get($header));
        }
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @return void
     * @covers ::check
     */
    public function testCheck(): void
    {
        $request = $this->createMock(Request::class);
        $request->query = new InputBag(['deep' => true]);
        $request->headers = new HeaderBag(['x-request-id' => ['__dummy_request_id__'], 'x-organization-id' => [123]]);

        $checkRequest = new CheckRequest($request);

        $response = $this->createMock(CheckResponse::class);
        $this->service->expects($this->once())->method('check')->with($checkRequest)->willReturn($response);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function (Envelope $envelope) use ($response) {
                $data = new ReflectionProperty(Envelope::class, 'data');
                $this->assertEquals($response, $data->getValue($envelope));

                return new JsonResponse($envelope);
            });

        $this->sut->check($request);
    }
}
