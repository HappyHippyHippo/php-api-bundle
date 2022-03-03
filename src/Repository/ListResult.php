<?php

namespace Hippy\Api\Repository;

use Hippy\Model\CollectionInterface;
use Hippy\Model\Model;

class ListResult extends Model implements ListResultInterface
{
    /** @var ListReportInterface */
    protected ListReportInterface $report;

    /**
     * @param CollectionInterface $collection
     * @param string $search
     * @param int $start
     * @param int $count
     * @param int $total
     */
    public function __construct(
        protected CollectionInterface $collection,
        string $search,
        int $start,
        int $count,
        int $total,
    ) {
        parent::__construct();
        $this->report = new ListReport($search, $start, $count, $total);
    }

    /**
     * @return CollectionInterface
     */
    public function getCollection(): CollectionInterface
    {
        return $this->collection;
    }

    /**
     * @return ListReportInterface
     */
    public function getReport(): ListReportInterface
    {
        return $this->report;
    }
}
