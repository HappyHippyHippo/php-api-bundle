<?php

namespace Hippy\Api\Repository;

use Hippy\Model\CollectionInterface;
use Hippy\Model\ModelInterface;

interface ListResultInterface extends ModelInterface
{
    /**
     * @return CollectionInterface
     */
    public function getCollection(): CollectionInterface;

    /**
     * @return ListReportInterface
     */
    public function getReport(): ListReportInterface;
}
