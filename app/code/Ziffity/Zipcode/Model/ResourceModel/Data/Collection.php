<?php

namespace Ziffity\Zipcode\Model\ResourceModel\Data;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'data_id';

    protected function _construct()
    {
        // @codingStandardsIgnoreEnd
        $this->_init('Ziffity\Zipcode\Model\Data', 'Ziffity\Zipcode\Model\ResourceModel\Data');
    }
}
