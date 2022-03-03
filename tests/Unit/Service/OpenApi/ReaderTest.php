<?php

namespace Hippy\Api\Tests\Unit\Service\OpenApi;

use Hippy\Api\Service\Base\OpenApi\Reader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @coversDefaultClass \Hippy\Api\Service\Base\OpenApi\Reader */
class ReaderTest extends TestCase
{
    /** @var Reader&MockObject */
    private Reader $sut;

    /**
     * @return void
     * @covers ::read
     * @covers ::readFile
     * @covers ::parseObject
     * @covers ::parseArray
     * @covers ::ref
     * @covers ::loadedRef
     * @covers ::remoteRef
     */
    public function testRead(): void
    {
        $file1content = (object) [
            'node1' => (object) [
                'node1.1' => (object) [
                    'node1.1.1' => (object) ['$ref' => '#/local/ref'],
                    'node1.1.2' => (object) ['$ref' => 'file2.yaml'],
                    'node1.1.3' => (object) ['$ref' => 'file2.yaml#/node1'],
                ],
                'node1.2' => [
                    (object) ['$ref' => '#/local/ref1'],
                    (object) ['$ref' => '#/local/ref2'],
                ],
                'node1.3' => [
                    [
                        'node1.3.1' => (object) ['data' => 'local ref 1.3.1 string'],
                        'node1.3.2' => (object) ['data' => 'local ref 1.3.2 string'],
                        'node1.3.3' => (object) ['data' => 'local ref 1.3.3 string'],
                    ],
                ],
            ],
            'local' => (object) [
                'ref' => (object) ['data' => 'local ref string'],
                'ref1' => (object) ['data' => 'local ref 1 string'],
                'ref2' => (object) ['data' => 'local ref 2 string'],
            ],
        ];

        $file2content = (object) [
            'node1' => (object) ['data' => 'remote ref string'],
        ];

        $expected = (object) [
            'node1' => (object) [
                'node1.1' => (object) [
                    'node1.1.1' => (object) ['data' => 'local ref string'],
                    'node1.1.2' => (object) ['node1' => (object) ['data' => 'remote ref string']],
                    'node1.1.3' => (object) ['data' => 'remote ref string'],
                ],
                'node1.2' => [
                    (object) ['data' => 'local ref 1 string'],
                    (object) ['data' => 'local ref 2 string'],
                ],
                'node1.3' => [
                    [
                        'node1.3.1' => (object) ['data' => 'local ref 1.3.1 string'],
                        'node1.3.2' => (object) ['data' => 'local ref 1.3.2 string'],
                        'node1.3.3' => (object) ['data' => 'local ref 1.3.3 string'],
                    ],
                ],
            ],
            'local' => (object) [
                'ref' => (object) ['data' => 'local ref string'],
                'ref1' => (object) ['data' => 'local ref 1 string'],
                'ref2' => (object) ['data' => 'local ref 2 string'],
            ],
        ];

        $this->sut
            ->expects($this->exactly(2))
            ->method('readYamlFile')
            ->withConsecutive(['/path/file1.yaml'], ['/path/file2.yaml'])
            ->willReturnOnConsecutiveCalls($file1content, $file2content);

        $this->assertEquals($expected, $this->sut->read('/path/file1.yaml'));
    }

    /**
     * @return void
     * @covers ::read
     * @covers ::readFile
     * @covers ::parseObject
     * @covers ::parseArray
     * @covers ::ref
     * @covers ::loadedRef
     * @covers ::remoteRef
     */
    public function testReadThrowOninvalidRef(): void
    {
        $file1content = (object) [
            'node1' => (object) [
                'node1.1' => (object) [
                    'node1.1.1' => (object) ['$ref' => '#/invalid'],
                ],
            ],
        ];

        $this->sut
            ->expects($this->once())
            ->method('readYamlFile')
            ->with('/path/file1.yaml')
            ->willReturn($file1content);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('unable to reference the openapi entry : #/invalid');
        $this->sut->read('/path/file1.yaml');
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->sut = $this->getMockBuilder(Reader::class)
            ->onlyMethods(['readYamlFile'])
            ->getMock();
    }
}
