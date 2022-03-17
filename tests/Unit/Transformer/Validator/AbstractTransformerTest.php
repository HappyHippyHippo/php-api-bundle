<?php

namespace Hippy\Api\Tests\Unit\Transformer\Validator;

use Hippy\Api\Transformer\Validator\AbstractTransformer;
use Hippy\Error\ErrorCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;

/** @coversDefaultClass \Hippy\Api\Transformer\Validator\AbstractTransformer */
class AbstractTransformerTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::transform
     */
    public function testTransformReturnNullIfParamIsNotConfigured(): void
    {
        $parameter = '__dummy_parameter__';

        $sut = $this->getMockForAbstractClass(AbstractTransformer::class, [[]]);

        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolation->expects($this->once())->method('getPropertyPath')->willReturn($parameter);

        $this->assertNull($sut->transform($constraintViolation));
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::transform
     * @covers ::violationToErrorCode
     */
    public function testTransformReturnExpectedError(): void
    {
        $parameter = '__dummy_parameter__';
        $parameterCode = 123;
        $violationCode = NotBlank::IS_BLANK_ERROR;
        $violationMessage = '__dummy_error_message__';

        $sut = $this->getMockForAbstractClass(AbstractTransformer::class, [[$parameter => $parameterCode]]);

        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolation->expects($this->once())->method('getPropertyPath')->willReturn($parameter);
        $constraintViolation->expects($this->once())->method('getCode')->willReturn($violationCode);
        $constraintViolation->expects($this->once())->method('getMessage')->willReturn($violationMessage);

        $result = $sut->transform($constraintViolation);
        if (is_null($result)) {
            $this->fail('transformation didnt return a valid error instance');
        }

        $this->assertNotNull($result);
        $this->assertEquals('123', $result->getParam());
        $this->assertEquals('370', $result->getCode());
        $this->assertEquals($violationMessage, $result->getMessage());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::transform
     * @covers ::violationToErrorCode
     */
    public function testTransformReturnUnExpectedError(): void
    {
        $parameter = '__dummy_parameter__';
        $parameterCode = 123;
        $violationMessage = '__dummy_error_message__';

        $sut = $this->getMockForAbstractClass(AbstractTransformer::class, [[$parameter => $parameterCode]]);

        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolation->expects($this->once())->method('getPropertyPath')->willReturn($parameter);
        $constraintViolation->expects($this->once())->method('getMessage')->willReturn($violationMessage);

        $result = $sut->transform($constraintViolation);
        if (is_null($result)) {
            $this->fail('transformation didnt return a valid error instance');
        }

        $this->assertNotNull($result);
        $this->assertEquals('123', $result->getParam());
        $this->assertEquals('460', $result->getCode());
        $this->assertEquals($violationMessage, $result->getMessage());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::transform
     * @covers ::violationToErrorCode
     */
    public function testTransformReturnExpectedErrorEvenOnParameterInnerHit(): void
    {
        $parameter = '__dummy_parameter__';
        $parameterPath = '__dummy[]_parameter[2]__';
        $parameterCode = 123;
        $violationCode = NotBlank::IS_BLANK_ERROR;
        $violationMessage = '__dummy_error_message__';

        $sut = $this->getMockForAbstractClass(AbstractTransformer::class, [[$parameter => $parameterCode]]);

        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolation->expects($this->once())->method('getPropertyPath')->willReturn($parameterPath);
        $constraintViolation->expects($this->once())->method('getCode')->willReturn($violationCode);
        $constraintViolation->expects($this->once())->method('getMessage')->willReturn($violationMessage);

        $result = $sut->transform($constraintViolation);
        if (is_null($result)) {
            $this->fail('transformation didnt return a valid error instance');
        }

        $this->assertNotNull($result);
        $this->assertEquals('123', $result->getParam());
        $this->assertEquals('370', $result->getCode());
        $this->assertEquals($violationMessage, $result->getMessage());
    }

    /**
     * @return void
     * @covers ::getStatusCode
     */
    public function testGetStatusCodeReturnDefaultBadRequestStatusCode(): void
    {
        $errors = $this->createMock(ErrorCollection::class);
        $sut = $this->getMockForAbstractClass(AbstractTransformer::class, [[]]);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $sut->getStatusCode($errors));
    }
}
