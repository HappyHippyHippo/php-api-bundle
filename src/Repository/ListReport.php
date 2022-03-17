<?php

namespace Hippy\Api\Repository;

use Hippy\Model\Model;

/**
 * @method string getSearch()
 * @method int getStart()
 * @method int getCount()
 * @method int getTotal()
 * @method string getPrev()
 * @method string getNext()
 */
class ListReport extends Model
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
}
