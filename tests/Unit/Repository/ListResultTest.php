<?php

namespace Hippy\Api\Tests\Unit\Repository;

use Hippy\Api\Repository\ListResult;
use Hippy\Model\CollectionInterface;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Repository\ListResult */
class ListResultTest extends TestCase
{
    /**
     * @return void
     * @covers ::__construct
     * @covers ::getCollection
     * @covers ::getReport
     */
    public function testConstructor(): void
    {
        $collection = $this->createMock(CollectionInterface::class);
        $search = '__dummy_search__';
        $start = 123;
        $count = 456;
        $total = 789;

        $sut = new ListResult($collection, $search, $start, $count, $total);

        $this->assertSame($collection, $sut->getCollection());
        $this->assertEquals($search, $sut->getReport()->getSearch());
        $this->assertEquals($start, $sut->getReport()->getStart());
        $this->assertEquals($count, $sut->getReport()->getCount());
        $this->assertEquals($total, $sut->getReport()->getTotal());
    }
}
