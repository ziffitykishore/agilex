<?php

namespace Ziffity\Pickupdate\Model\ResourceModel\Holidays;

class Collection extends \Ziffity\Pickupdate\Model\ResourceModel\DateCollectionAbstract
{
    protected function _construct()
    {
        $this->_init('Ziffity\Pickupdate\Model\Holidays', 'Ziffity\Pickupdate\Model\ResourceModel\Holidays');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
