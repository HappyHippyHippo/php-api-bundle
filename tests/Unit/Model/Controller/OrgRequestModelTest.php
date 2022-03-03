<?php

namespace Hippy\Api\Tests\Unit\Model\Controller;

use Hippy\Api\Model\Controller\OrgRequestModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/** @coversDefaultClass \Hippy\Api\Model\Controller\OrgRequestModel */
class OrgRequestModelTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaderOrgId
     */
    public function testConstruct(): void
    {
        $request = new Request();
        $model = new OrgRequestModel($request);

        $this->assertEquals([], $model->jsonSerialize());
        $this->assertEquals(0, $model->getHeaderOrgId());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getRequest
     */
    public function testGetRequest(): void
    {
        $request = new Request();
        $model = new OrgRequestModel($request);

        $this->assertSame($request, $model->getRequest());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getHeaderOrgId
     */
    public function testGetHeaderOrganizationId(): void
    {
        $header = '123';
        $request = new Request();
        $request->headers->set('X-Organization-ID', $header);
        $model = new OrgRequestModel($request);

        $this->assertSame(123, $model->getHeaderOrgId());
    }
}
