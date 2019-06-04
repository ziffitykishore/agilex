<?php

namespace Ziffity\Pickupdate\Model\ResourceModel\Pickupdate;

class Collection extends \Ziffity\Pickupdate\Model\ResourceModel\DateCollectionAbstract
{
    protected function _construct()
    {
        $this->_init('Ziffity\Pickupdate\Model\Pickupdate', 'Ziffity\Pickupdate\Model\ResourceModel\Pickupdate');
    }

    public function getOlderThan($start)
    {
        $this->getSelect()
            ->where('`date` <> \'0000-00-00\'')
            ->where('`date` <> \'1970-01-01\'')
            ->where('`date` >= ?', $start)
            ->where('`active` = \'1\'');

        return $this;
    }

    public function joinTinterval()
    {
        $this->getSelect()
            ->joinLeft(
                ['ti' => $this->getTable('ziffity_pickupdate_tinterval')],
                'main_table.tinterval_id = ti.tinterval_id',
                ['qty_order' => 'COUNT(main_table.pickupdate_id)']
            )
            ->where('ti.quota > 0 AND main_table.date is not null')
            ->group('ti.tinterval_id')
            ->group('main_table.date')
            ->having('qty_order >= MAX(ti.quota)');

        return $this;
    }
}
