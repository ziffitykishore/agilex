<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Observer;

class CatalogProductIsSalableAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $_coreHelperData = null;
    protected $_modelStock = null;
    protected $_storeManager = null;
    protected $_stockHelper = null;
    protected $_modelPos = null;

    /**
     * CatalogProductIsSalableAfter constructor.
     * @param \Wyomind\Core\Helper\Data $coreHelperData
     * @param \Wyomind\AdvancedInventory\Model\Stock $modelStock
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\AdvancedInventory\Helper\Stock $stockHelper
     * @param \Wyomind\PointOfSale\Model\PointOfSale $modelPointOfSale
     */
    public function __construct(
        \Wyomind\Core\Helper\Data $coreHelperData, \Wyomind\AdvancedInventory\Model\Stock $modelStock,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\AdvancedInventory\Helper\Stock $stockHelper,
        \Wyomind\PointOfSale\Model\PointOfSale $modelPointOfSale
    )
    {
        $this->_coreHelperData = $coreHelperData;
        $this->_modelStock = $modelStock;
        $this->_storeManager = $storeManager;
        $this->_stockHelper = $stockHelper;
        $this->_modelPos = $modelPointOfSale;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $this->_storeManager->getStore()->getStoreId();
        $placeIds = $this->_modelPos->getPlacesByStoreId($storeId);
        if ($this->_coreHelperData->getStoreConfig('advancedinventory/settings/enabled')) {
           
            $rtn = false;
            $product = $observer->getProduct();

            if (in_array($product->getTypeId(), ['downloadable', 'virtual'])) {
                return;
            }

            if ($this->_stockHelper->getStockItem($product->getId())) {
                $stockStatus = $this->_stockHelper->getStockItem($product->getId())->getIsInStock();
                if ($product->getStatus() == 2 || !$stockStatus || $product->getDisableAddToCart()) {
                    $observer->getSalable()->setIsSalable(false);
                    return;
                }
            }

            if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                return;
            } else {
                if ($this->_modelStock->isMultiStockEnabledByProductId($product->getId())) {
                    $rtn = $this->isAvailable($product, $placeIds);
                } else {
                    $rtn = null;
                }
            }

            if ($rtn !== null) {
                $observer->getSalable()->setIsSalable($rtn);
            }
        }
    }

    public function isAvailable($product, $placeIds)
    {
        foreach ($placeIds->getData() as $pos) {
            //  echo ">>> isAvailable, " . $product->getId() .", ". $pos['place_id'] . "\n";
            $productId = $product->getId();
            $stock = $this->_modelStock->getStockSettings($productId, $pos['place_id']);
           
            if ($stock->getStockStatus()) {
                return true;
            }
        }

        return false;
    }
}
