<?php

namespace Ziffity\Pickupdate\Model\ResourceModel\Dinterval;

class Collection extends \Ziffity\Pickupdate\Model\ResourceModel\DateCollectionAbstract
{
    protected function _construct()
    {
        $this->_init('Ziffity\Pickupdate\Model\Dinterval', 'Ziffity\Pickupdate\Model\ResourceModel\Dinterval');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param int $currentStoreId
     *
     * @return \Ziffity\Pickupdate\Model\Dinterval[]
     */
    public function filterByStore($currentStoreId)
    {
        $dintervals = [];

        foreach ($this as $item) {
            $storeIds = trim($item->getData('store_ids'), ',');
            $storeIds = explode(',', $storeIds);
            if (!in_array($currentStoreId, $storeIds) && !in_array(0, $storeIds)) {
                continue;
            }
            $dintervals[] = $item;
        }

        return $dintervals;
    }
}
