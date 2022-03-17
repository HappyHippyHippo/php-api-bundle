<?php

namespace Hippy\Api\Tests\Unit\Validator;

use Hippy\Api\Transformer\Validator\TransformerInterface;
use Hippy\Api\Validator\AbstractValidator;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Hippy\Exception\Exception;
use Hippy\Model\Model;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

/** @coversDefaultClass \Hippy\Api\Validator\AbstractValidator */
class AbstractValidatorTest extends TestCase
{
    /** @var SymfonyValidatorInterface&MockObject */
    private SymfonyValidatorInterface $validator;

    /** @var TransformerInterface&MockObject */
    private TransformerInterface $transformer;

    /** @var AbstractValidator&MockObject */
    private AbstractValidator $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = $this->createMock(SymfonyValidatorInterface::class);
        $this->transformer = $this->createMock(TransformerInterface::class);
        $this->sut = $this->getMockForAbstractClass(AbstractValidator::class, [$this->validator]);
    }

    /**
     * @return void
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $validator = new ReflectionProperty(AbstractValidator::class, 'validator');
        $this->assertSame($this->validator, $validator->getValue($this->sut));
    }

    /**
     * @return void
     * @covers ::process
     * @throws ReflectionException
     */
    public function testProcessThrowFoundedErrors(): void
    {
        $errors = $this->generateErrors();
        $errorCollection = new ErrorCollection($errors);

        $this->transformer
            ->expects($this->once())
            ->method('getStatusCode')
            ->with($errorCollection)
            ->willReturn(Response::HTTP_BAD_REQUEST);

        $model = $this->generateGenericViolationsModel($errors);

        try {
            $this->invoke($model);
        } catch (Exception $exception) { // @phpstan-ignore-line
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $exception->getStatusCode());
            $this->assertEquals($errorCollection, $exception->getErrors());

            return;
        }

        $this->fail('Did not throw the expected exception');
    }

    /**
     * @return void
     * @covers ::process
     * @throws ReflectionException
     */
    public function testProcessReturnModelIfNoErrorWasTransformed(): void
    {
        $this->transformer->expects($this->never())->method('getStatusCode');
        $model = $this->generateNonTransformableViolationsModel();

        $this->assertSame($model, $this->invoke($model));
    }

    /**
     * @return void
     * @covers ::process
     * @throws ReflectionException
     */
    public function testProcessReturnModelIfNoError(): void
    {
        $model = $this->createMock(Model::class);

        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->expects($this->once())->method('count')->willReturn(0);

        $this->transformer->expects($this->never())->method('getStatusCode');
        $this->validator->expects($this->once())->method('validate')->with($model)->willReturn($errors);

        $this->assertSame($model, $this->invoke($model));
    }

    /**
     * @return array<int, Error&MockObject>
     */
    private function generateErrors(): array
    {
        return [
            $this->createMock(Error::class),
            $this->createMock(Error::class),
            $this->createMock(Error::class),
        ];
    }

    /**
     * @param array<int, Error&MockObject> $errors
     * @return Model
     */
    private function generateGenericViolationsModel(array $errors): Model
    {
        $model = $this->createMock(Model::class);

        $violations = [
            $this->createMock(ConstraintViolationInterface::class),
            $this->createMock(ConstraintViolationInterface::class),
            $this->createMock(ConstraintViolationInterface::class),
        ];
        $transformCalls = array_map(function ($violation) {
            return [$violation];
        }, $violations);
        $valid = array_fill(0, count($violations), true);
        $valid[] = false;

        $violationsList = $this->createMock(ConstraintViolationListInterface::class);
        $violationsList->method('count')->willReturn(count($violations));
        $violationsList->method('rewind');
        $violationsList->method('next');
        $violationsList->method('valid')->willReturnOnConsecutiveCalls(...$valid);
        $violationsList->method('current')->willReturnOnConsecutiveCalls(...$violations);
        $this->validator->expects($this->once())->method('validate')->with($model)->willReturn($violationsList);

        $this->transformer
            ->expects($this->exactly(count($violations)))
            ->method('transform')
            ->withConsecutive(...$transformCalls)
            ->willReturnOnConsecutiveCalls(...$errors);

        return $model;
    }

    /**
     * @return Model
     */
    private function generateNonTransformableViolationsModel(): Model
    {
        $model = $this->createMock(Model::class);

        $violations = [
            $this->createMock(ConstraintViolationInterface::class),
            $this->createMock(ConstraintViolationInterface::class),
            $this->createMock(ConstraintViolationInterface::class),
        ];
        $transformCalls = array_map(function ($violation) {
            return [$violation];
        }, $violations);
        $errors = [null, null, null];
        $valid = array_fill(0, count($violations), true);
        $valid[] = false;

        $violationsList = $this->createMock(ConstraintViolationListInterface::class);
        $violationsList->method('count')->willReturn(count($violations));
        $violationsList->method('rewind');
        $violationsList->method('next');
        $violationsList->method('valid')->willReturnOnConsecutiveCalls(...$valid);
        $violationsList->method('current')->willReturnOnConsecutiveCalls(...$violations);
        $this->validator->expects($this->once())->method('validate')->with($model)->willReturn($violationsList);

        $this->transformer
            ->expects($this->exactly(count($violations)))
            ->method('transform')
            ->withConsecutive(...$transformCalls)
            ->willReturnOnConsecutiveCalls(...$errors);

        return $model;
    }

    /**
     * @param Model $model
     * @return Model
     * @throws ReflectionException
     */
    private function invoke(Model $model): Model
    {
        $invoker = new ReflectionMethod(AbstractValidator::class, 'process');
        return $invoker->invoke($this->sut, $model, $this->transformer);
    }
}
