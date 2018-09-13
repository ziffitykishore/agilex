<?php

namespace Ziffity\Webforms\Model\ResourceModel\Data;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'cust_id';

    protected function _construct()
    {
        
        $this->_init('Ziffity\Webforms\Model\Data', 'Ziffity\Webforms\Model\ResourceModel\Data');
    }
}
