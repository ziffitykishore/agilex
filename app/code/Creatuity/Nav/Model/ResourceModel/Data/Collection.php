<?php

namespace Creatuity\Nav\Model\ResourceModel\Data;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init(Creatuity\Nav\Model\Data::class, Creatuity\Nav\Model\ResourceModel\Data::class);
    }
}
