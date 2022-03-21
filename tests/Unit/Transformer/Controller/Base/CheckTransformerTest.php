<?php

namespace Hippy\Api\Tests\Unit\Transformer\Controller\Base;

use Hippy\Api\Transformer\Controller\Base\CheckTransformer;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \Hippy\Api\Transformer\Controller\Base\CheckTransformer */
class CheckTransformerTest extends TestCase
{
    /**
     * @covers ::__construct
     * @return void
     */
    public function testConstructor(): void
    {
        $prop = new ReflectionProperty(CheckTransformer::class, 'paramMap');
        $this->assertEquals([
            'deep' => 1,
        ], $prop->getValue(new CheckTransformer()));
    }
}
