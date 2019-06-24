<?php

namespace Ziffity\Pickupdate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Tinterval extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('ziffity_pickupdate_tinterval', 'tinterval_id');
    }
}
