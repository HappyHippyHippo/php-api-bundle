<?php

namespace Hippy\Api\Repository;

use Hippy\Model\ModelInterface;

interface ListReportInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getSearch(): string;

    /**
     * @return int
     */
    public function getStart(): int;

    /**
     * @return int
     */
    public function getCount(): int;

    /**
     * @return int
     */
    public function getTotal(): int;

    /**
     * @return string
     */
    public function getPrev(): string;

    /**
     * @return string
     */
    public function getNext(): string;
}
