<?php

/* *
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Model\System\Config\Source;

class Assignation
{

    protected $_posCollection = null;

    public function __construct(
    \Wyomind\PointOfSale\Model\ResourceModel\PointOfSale\Collection $posCollection,
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        if ($request->getParam('store')) {
            $this->_posCollection = $posCollection->getPlacesByStoreId($request->getParam('store'), null);
        } elseif ($request->getParam('website')) {
            $website = $storeManager->getWebsite($request->getParam('website'));
            $stores = $website->getStoreCollection();
            $where = [];
            foreach ($stores as $store) {
                $where[] = "FIND_IN_SET(" . $store->getId() . ",main_table.store_id)";
            }
            $this->_posCollection = $posCollection;
            $this->_posCollection->getSelect()->where(implode(" OR ", $where));
        } else {
            $this->_posCollection = $posCollection;
        }
    }

    public function toOptionArray()
    {
        $data = [['value' => -2, 'label' => 'Automatic']];
        $data[] = ['value' => -1, 'label' => 'No Assignation'];
        foreach ($this->_posCollection as $pos) {
            $data[] = ['value' => $pos->getPlaceId(), 'label' => "[" . $pos->getStoreCode() . "] " . $pos->getName()];
        }
        return $data;
    }

}
