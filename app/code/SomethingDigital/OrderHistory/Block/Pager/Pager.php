<?php

namespace SomethingDigital\OrderHistory\Block\Pager;


class Pager
{

    public function getLastNum($collection, $limit)
    {
        return min($collection->count(), $collection->getCurPage()*$limit);
    }
}
