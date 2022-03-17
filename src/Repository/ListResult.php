<?php

namespace Hippy\Api\Repository;

use Hippy\Model\Collection;
use Hippy\Model\Model;

class ListResult extends Model
{
    /** @var ListReport */
    protected ListReport $report;

    /**
     * @param Collection $collection
     * @param string $search
     * @param int $start
     * @param int $count
     * @param int $total
     */
    public function __construct(
        protected Collection $collection,
        string $search,
        int $start,
        int $count,
        int $total,
    ) {
        parent::__construct();
        $this->report = new ListReport($search, $start, $count, $total);
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return ListReport
     */
    public function getReport(): ListReport
    {
        return $this->report;
    }
}
