<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Model;

class Stock extends \Magento\Framework\Model\AbstractModel
{
    protected $_stockRegistry = [];
    protected $_stockRegistryEnabled = true;
    protected $_connection = null;
    protected $_itemModel = null;
    protected $_posFactory = null;
    protected $_stockFactory = null;
    protected $_itemFactory = null;
    protected $_productCollectionFactory = null;
    protected $_resourceConnection = null;
    protected $_pointOfSaleCollectionFactory = null;
    protected $_helperCore = null;
    protected $_requestFactory = null;

    /**
     * Stock constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Item $itemModel
     * @param ResourceModel\PointOfSale\CollectionFactory $pointOfSaleCollectionFactory
     * @param \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory
     * @param ResourceModel\Stock\CollectionFactory $stockFactory
     * @param ResourceModel\Item\CollectionFactory $itemFactory
     * @param ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Wyomind\Core\Helper\Data $helperCore
     * @param \Magento\Framework\App\RequestFactory $requestFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $abstractResource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $abstactDb
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry,
        Item $itemModel,
        ResourceModel\PointOfSale\CollectionFactory $pointOfSaleCollectionFactory,
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory,
        ResourceModel\Stock\CollectionFactory $stockFactory,
        ResourceModel\Item\CollectionFactory $itemFactory,
        ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Wyomind\Core\Helper\Data $helperCore,
        \Magento\Framework\App\RequestFactory $requestFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $abstractResource = null,
        \Magento\Framework\Data\Collection\AbstractDb $abstactDb = null, array $data = []
    )
    {
        $this->_helperCore = $helperCore;
        $this->_resourceConnection = $resource;
        $this->_itemModel = $itemModel;
        $this->_pointOfSaleCollectionFactory = $pointOfSaleCollectionFactory;
        $this->_posFactory = $posFactory;
        $this->_stockFactory = $stockFactory;
        $this->_itemFactory = $itemFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_requestFactory = $requestFactory;

        parent::__construct($context, $registry, $abstractResource, $abstactDb, $data);
    }

    public function _construct()
    {
        $this->_init('Wyomind\AdvancedInventory\Model\ResourceModel\Stock');
    }

    protected function _getWriteConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->getResource()->getConnection('write');
        }

        return $this->_connection;
    }

    public function getStockByProductIdAndStoreIds($productId, $storeIds)
    {
        $result = $this->_pointOfSaleCollectionFactory->create()->getStockByProductIdAndStoreId($productId, $storeIds);

        return $result;
    }

    public function getStockByProductIdAndPlaceId($productId, $placeId)
    {
        $collection = $this->_stockFactory->create()
                ->addFieldToFilter('main_table.product_id', ['eq' => $productId])
                ->addFieldToFilter('place_id', ['eq' => $placeId]);

        return $collection->getFirstItem();
    }

    public function isMultiStockEnabledByProductId($productId)
    {
        $collection = $this->_itemFactory->create()->addFieldToFilter('product_id', ['eq' => $productId]);

        return $collection->getFirstItem()->getMultistockEnabled();
    }


    public function getStockSettings($productId = false, $placeId = false, $placeIds = [], $itemId = false)
    {
        $qty = (float)($this->_requestFactory->create()->getParam("qty"));
        if ($placeId) {
            $ids = $placeId;
            $placeIds = [$placeId];
        } elseif (count($placeIds)) {
            $ids = implode("-", $placeIds);
        } else {
            $ids = 0;
            $placeIds = [];
        }
        //if (!isset($this->_stockRegistry[$productId][$ids]) || !$this->_stockRegistryEnabled) {
            $inventory = $this->_productCollectionFactory->create()->getStockSettings($productId, $placeId, $itemId, $placeIds);

            if (!$inventory->getMultistockEnabled() && $placeId) {
                $inventory->setBackorderableAtStockLevel($inventory->getDefaultBackorderableAtStockLevel());
                $inventory->setManagedAtStockLevel($inventory->getDefaultManagedAtStockLevel());
            }
            $autoStock = ($this->_helperCore->getStoreConfig("advancedinventory/settings/auto_update_stock_status"));

            // No qty managed
            if (!$inventory->getManagedAtProductLevel() && !$inventory->getManagedAtStockLevel()) {
                $inventory->setStockStatus(true);
            } else {
                // Qty managed
                // Multistock
                if ($inventory->getMultistockEnabled()) {
                    if (!$autoStock && !$inventory->getIsInStock()) {
                        $inventory->setStockStatus(false);
                    } elseif (($inventory->getQuantityInStock() > 0 && $inventory->getQuantityInStock() >= $inventory->getMinQty()) || $inventory->getBackorderableAtStockLevel()) {
                        $inventory->setStockStatus(true);
                    } else {
                        $inventory->setStockStatus(false);
                    }
                } else { // No multistock
                    if ((((float) $inventory->getQty() > 0 && (float) $inventory->getQty() >= (float) $inventory->getMinQty()) || $inventory->getBackorderableAtProductLevel()) && $autoStock) {
                        $inventory->setStockStatus(true);
                    } else {
                        $inventory->setStockStatus();
                    }
                }
            }

            $this->_stockRegistry[$productId][$ids] = $inventory;
        //}

        return $this->_stockRegistry[$productId][$ids];
    }
}
