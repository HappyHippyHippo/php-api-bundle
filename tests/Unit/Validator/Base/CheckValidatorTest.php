<?php

namespace Hippy\Api\Tests\Unit\Validator\Base;

use Hippy\Api\Model\Controller\Check\CheckRequest;
use Hippy\Api\Transformer\Controller\Base\CheckTransformer;
use Hippy\Api\Validator\Base\CheckValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @coversDefaultClass \Hippy\Api\Validator\Base\CheckValidator */
class CheckValidatorTest extends TestCase
{
    /** @var ValidatorInterface&MockObject */
    private ValidatorInterface $validator;

    /** @var CheckTransformer&MockObject */
    private CheckTransformer $transformer;

    /** @var CheckValidator&MockObject */
    private CheckValidator $sut;

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $validator = new ReflectionProperty(CheckValidator::class, 'validator');
        $this->assertEquals($this->validator, $validator->getValue($this->sut));

        $transformer = new ReflectionProperty(CheckValidator::class, 'transformer');
        $this->assertEquals($this->transformer, $transformer->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::validate
     */
    public function testValidate(): void
    {
        $request = $this->createMock(Request::class);
        $request->query = new InputBag(['deep' => true]);

        $this->sut
            ->expects($this->once())
            ->method('process')
            ->with($this->callback(function (CheckRequest $model) use ($request) {
                return $this->assertRequestModel($request, $model);
            }), $this->transformer);

        $this->assertEquals(new CheckRequest($request), $this->sut->validate($request));
    }

    /**
     * @param Request $request
     * @param CheckRequest $model
     * @return bool
     */
    private function assertRequestModel(Request $request, CheckRequest $model): bool
    {
        return $model->getRequest() === $request
            && $model->isDeep() == true;
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->transformer = $this->createMock(CheckTransformer::class);
        $this->sut = $this->getMockBuilder(CheckValidator::class)
            ->setConstructorArgs([$this->validator, $this->transformer])
            ->onlyMethods(['process'])
            ->getMock();
    }
}
