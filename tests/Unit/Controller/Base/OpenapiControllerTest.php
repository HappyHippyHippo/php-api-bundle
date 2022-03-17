<?php

namespace Hippy\Api\Tests\Unit\Controller\Base;

use Hippy\Api\Config\ApiConfig;
use Hippy\Api\Controller\Base\OpenapiController;
use Hippy\Api\Service\Base\OpenapiService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \Hippy\Api\Controller\Base\OpenapiController */
class OpenapiControllerTest extends TestCase
{
    /** @var ApiConfig&MockObject */
    private ApiConfig $config;

    /** @var OpenapiService&MockObject */
    private OpenapiService $service;

    /** @var OpenapiController&MockObject */
    private OpenapiController $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ApiConfig::class);
        $this->service = $this->createMock(OpenapiService::class);
        $this->sut = $this->getMockBuilder(OpenapiController::class)
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
        $prop = new ReflectionProperty(OpenapiController::class, 'endpointCode');
        $this->assertEquals(3, $prop->getValue($this->sut));
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
     * @covers ::__construct
     * @covers ::isEnabled
     * @covers ::openapi
     */
    public function testExecGet()
    {
        $content = '__dummy_response__';
        $this->service->expects($this->once())->method('process')->willReturn($content);

        $this->config
            ->expects($this->once())
            ->method('isEndpointOpenapiEnabled')
            ->willReturn(true);

        $response = $this->sut->openapi();

        $this->assertEquals($content, $response->getContent());
        $this->assertEquals('text/vnd.yaml', $response->headers->get('Content-Type'));
    }
}
