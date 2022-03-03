<?php

namespace Hippy\Api\Repository;

use Hippy\Model\Model;

class ListReport extends Model implements ListReportInterface
{
    /** @var string */
    protected string $prev;

    /** @var string */
    protected string $next;

    /**
     * @param string $search
     * @param int $start
     * @param int $count
     * @param int $total
     */
    public function __construct(
        protected string $search,
        protected int $start,
        protected int $count,
        protected int $total,
    ) {
        parent::__construct();

        $prev = max(0, $start - $count);
        $next = $start + ($start + $count >= $total ? 0 : $count);

        $this->prev = $prev != $start ? sprintf("?search=%s&start=%d&count=%d", $search, $prev, $count) : '';
        $this->next = $next != $start ? sprintf("?search=%s&start=%d&count=%d", $search, $next, $count) : '';
    }

    /**
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getPrev(): string
    {
        return $this->prev;
    }

    /**
     * @return string
     */
    public function getNext(): string
    {
        return $this->next;
    }
}
