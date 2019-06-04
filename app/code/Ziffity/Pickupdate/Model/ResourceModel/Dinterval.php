<?php

namespace Ziffity\Pickupdate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Dinterval extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('ziffity_pickupdate_dinterval', 'dinterval_id');
    }
}
