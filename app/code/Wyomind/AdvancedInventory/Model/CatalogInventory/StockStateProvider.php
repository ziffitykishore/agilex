<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Model\CatalogInventory;

class StockStateProvider
{
    protected $_modelStock = null;
    protected $_posFactory = null;
    protected $_storeManager = null;
    protected $_assignationHelper = null;

    /**
     * StockStateProvider constructor.
     * @param \Wyomind\AdvancedInventory\Model\Stock $modelStock
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory
     * @param \Wyomind\AdvancedInventory\Helper\Assignation $assignationHelper
     */
    public function __construct(
        \Wyomind\AdvancedInventory\Model\Stock $modelStock,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory,
        \Wyomind\AdvancedInventory\Helper\Assignation $assignationHelper
    )
    {
        $this->_modelStock = $modelStock;
        $this->_posFactory = $posFactory;
        $this->_storeManager = $storeManager;
        $this->_assignationHelper = $assignationHelper;
    }

    public function beforeCheckQuoteItemQty(
        $subsject,
        $item
    )
    {
        if (!$this->_modelStock->isMultiStockEnabledByProductId($item->getProductId())) {
            return;
        }

        $storeId = $this->_storeManager->getStore()->getStoreId();
        
        if($this->_assignationHelper->isAdminQuote()) {
            $storeId = $this->_assignationHelper->getAdminQuoteStoreId();
        }
        
        $places = $this->_posFactory->create()->getPlacesByStoreId($storeId);
        $placeIds = [];
        foreach ($places as $place) {
            $placeIds[] = $place->getPlaceId();
        }

        $inventory = $this->_modelStock->getStockSettings($item->getProductId(), false, $placeIds);

        $qty = 0;
        $backOrderableAtStockLevel = $inventory->getBackorderableAtStockLevel();
        $item->setBackorders($backOrderableAtStockLevel);

        if ($backOrderableAtStockLevel) {
            $item->setUseConfigBackorders(0);
        }

        foreach ($places as $place) {
            $qtyInStock = 'quantity_' . $place->getPlaceId() . "";
            $qty += ((int)$inventory[$qtyInStock] - (int)$inventory['min_qty']);
            
            $manageStock= 'manage_stock_' . $place->getPlaceId() . "";
            if(!$inventory[$manageStock]){
                $qty += INF;
            }
        }

        $qty += $item->getMinQty();
        $item->setQty($qty);
    }
}
