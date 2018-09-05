<?php

namespace Ziffity\Webforms\Model\ResourceModel\Catalog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'customer_id';

    protected function _construct()
    {

        $this->_init('Ziffity\Webforms\Model\Catalog','Ziffity\Webforms\Model\ResourceModel\Catalog');
    }
}
