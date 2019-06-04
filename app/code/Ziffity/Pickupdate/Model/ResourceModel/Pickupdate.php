<?php

namespace Ziffity\Pickupdate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Pickupdate extends AbstractDb
{
    const MAIN_TABLE = 'ziffity_pickupdate_pickupdate';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'pickupdate_id');
    }
}
