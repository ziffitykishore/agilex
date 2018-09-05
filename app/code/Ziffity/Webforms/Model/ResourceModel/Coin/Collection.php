<?php

namespace Ziffity\Webforms\Model\ResourceModel\Coin;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'customer_id';

    protected function _construct()
    {

        $this->_init('Ziffity\Webforms\Model\Coin','Ziffity\Webforms\Model\ResourceModel\Coin');
    }
}
