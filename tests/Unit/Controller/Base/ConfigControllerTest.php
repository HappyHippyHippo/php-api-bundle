<?php

namespace Hippy\Api\Tests\Unit\Controller\Base;

use Hippy\Api\Config\ApiConfigInterface;
use Hippy\Api\Controller\Base\ConfigController;
use Hippy\Api\Model\Controller\Config\ConfigResponse;
use Hippy\Api\Service\Base\ConfigService;
use Hippy\Model\Envelope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Controller\Base\ConfigController */
class ConfigControllerTest extends TestCase
{
    /** @var ApiConfigInterface&MockObject */
    private ApiConfigInterface $config;

    /** @var ConfigService&MockObject */
    private ConfigService $service;

    /** @var ConfigController&MockObject */
    private ConfigController $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfigInterface::class);
        $this->service = $this->createMock(ConfigService::class);
        $this->sut = $this->getMockBuilder(ConfigController::class)
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
        $prop = new ReflectionProperty(ConfigController::class, 'endpointCode');
        $this->assertEquals(4, $prop->getValue($this->sut));
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
     * @covers ::__construct
     * @covers ::isEnabled
     * @covers ::config
     */
    public function testConfig(): void
    {
        $response = $this->createMock(ConfigResponse::class);
        $this->service->expects($this->once())->method('process')->willReturn($response);

        $this->config
            ->expects($this->once())
            ->method('isEndpointConfigEnabled')
            ->willReturn(true);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function (Envelope $envelope) use ($response) {
                $data = new ReflectionProperty(Envelope::class, 'data');
                $this->assertEquals($response, $data->getValue($envelope));

                return new JsonResponse($envelope);
            });

        $this->sut->config();
    }
}
