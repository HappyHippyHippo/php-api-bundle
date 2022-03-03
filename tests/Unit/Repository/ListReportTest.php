<?php

namespace Hippy\Api\Tests\Unit\Repository;

use Hippy\Api\Repository\ListReport;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \Hippy\Api\Repository\ListReport */
class ListReportTest extends TestCase
{
    /**
     * @param string $search
     * @param int $start
     * @param int $count
     * @param int $total
     * @param string $expected
     * @return void
     * @covers ::__construct
     * @covers ::getPrev
     * @dataProvider providerForPrevTests
     */
    public function testPrev(string $search, int $start, int $count, int $total, string $expected): void
    {
        $this->assertEquals($expected, (new ListReport($search, $start, $count, $total))->getPrev());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForPrevTests(): array
    {
        return [
            'first page' => [
                'search' => '__dummy_search__',
                'start' => 0,
                'count' => 50,
                'total' => 200,
                'expected' => '',
            ],
            'mid-first page' => [
                'search' => '__dummy_search__',
                'start' => 25,
                'count' => 50,
                'total' => 200,
                'expected' => '?search=__dummy_search__&start=0&count=50',
            ],
            'second page' => [
                'search' => '__dummy_search__',
                'start' => 50,
                'count' => 50,
                'total' => 200,
                'expected' => '?search=__dummy_search__&start=0&count=50',
            ],
            'mid-second page' => [
                'search' => '__dummy_search__',
                'start' => 75,
                'count' => 50,
                'total' => 200,
                'expected' => '?search=__dummy_search__&start=25&count=50',
            ],
            'third page' => [
                'search' => '__dummy_search__',
                'start' => 100,
                'count' => 50,
                'total' => 200,
                'expected' => '?search=__dummy_search__&start=50&count=50',
            ],
        ];
    }

    /**
     * @param string $search
     * @param int $start
     * @param int $count
     * @param int $total
     * @param string $expected
     * @return void
     * @covers ::__construct
     * @covers ::getNext
     * @dataProvider providerForNextTests
     */
    public function testNext(string $search, int $start, int $count, int $total, string $expected): void
    {
        $this->assertEquals($expected, (new ListReport($search, $start, $count, $total))->getNext());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForNextTests(): array
    {
        return [
            'first page' => [
                'search' => '__dummy_search__',
                'start' => 0,
                'count' => 50,
                'total' => 200,
                'expected' => '?search=__dummy_search__&start=50&count=50',
            ],
            'mid-last page' => [
                'search' => '__dummy_search__',
                'start' => 175,
                'count' => 50,
                'total' => 200,
                'expected' => '',
            ],
            'last page' => [
                'search' => '__dummy_search__',
                'start' => 150,
                'count' => 50,
                'total' => 200,
                'expected' => '',
            ],
            'mid-pre-last page' => [
                'search' => '__dummy_search__',
                'start' => 125,
                'count' => 50,
                'total' => 200,
                'expected' => '?search=__dummy_search__&start=175&count=50',
            ],
            'pre-last-page page' => [
                'search' => '__dummy_search__',
                'start' => 100,
                'count' => 50,
                'total' => 200,
                'expected' => '?search=__dummy_search__&start=150&count=50',
            ],
        ];
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getSearch
     */
    public function testGetSearch(): void
    {
        $search = '__dummy_search__';
        $this->assertEquals($search, (new ListReport($search, 1, 1, 1))->getSearch());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getStart
     */
    public function testGetStart(): void
    {
        $start = 123;
        $this->assertEquals($start, (new ListReport('', $start, 1, 1))->getStart());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getCount
     */
    public function testGetCount(): void
    {
        $count = 123;
        $this->assertEquals($count, (new ListReport('', 1, $count, 1))->getCount());
    }

    /**
     * @return void
     * @covers ::__construct
     * @covers ::getTotal
     */
    public function testGetTotal(): void
    {
        $total = 123;
        $this->assertEquals($total, (new ListReport('', 1, 1, $total))->getTotal());
    }

    /**
     * @return void
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $search = '__dummy_search__';
        $start = 123;
        $count = 50;
        $total = 789;

        $sut = new ListReport($search, $start, $count, $total);

        $this->assertEquals([
            'search' => $search,
            'start' => $start,
            'count' => $count,
            'total' => $total,
            'prev' => '?search=__dummy_search__&start=73&count=50',
            'next' => '?search=__dummy_search__&start=173&count=50',
        ], $sut->jsonSerialize());
    }
}
