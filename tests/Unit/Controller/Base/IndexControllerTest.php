<?php

namespace Hippy\Api\Tests\Unit\Controller\Base;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Controller\Base\IndexController;
use Hippy\Api\Model\Controller\Index\IndexResponse;
use Hippy\Api\Service\Base\IndexService;
use Hippy\Model\Envelope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Controller\Base\IndexController */
class IndexControllerTest extends TestCase
{
    /** @var ApiConfigInterface&MockObject */
    private ApiConfigInterface $config;

    /** @var IndexService&MockObject */
    private IndexService $service;

    /** @var IndexController&MockObject */
    private IndexController $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfigInterface::class);
        $this->service = $this->createMock(IndexService::class);
        $this->sut = $this->getMockBuilder(IndexController::class)
            ->setConstructorArgs([$this->config, $this->service])
            ->onlyMethods(['json'])
            ->getMock();
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstructorStoreTheCorrectDomainCode()
    {
        $prop = new ReflectionProperty(IndexController::class, 'endpointCode');
        $this->assertEquals(1, $prop->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::preflight
     */
    public function testPreflight()
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
     * @covers ::index
     */
    public function testIndex()
    {
        $response = $this->createMock(IndexResponse::class);
        $this->service->expects($this->once())->method('process')->willReturn($response);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function (Envelope $envelope) use ($response) {
                $data = new ReflectionProperty(Envelope::class, 'data');
                $this->assertEquals($response, $data->getValue($envelope));

                return new JsonResponse($envelope);
            });

        $this->sut->index();
    }
}
