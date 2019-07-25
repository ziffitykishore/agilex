<?php

namespace Ziffity\Inventory\Block;

use Magento\Framework\View\Element\Template;

class SourceStock extends Template
{
    /**
     *
     * @var \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku
     */
    protected $sourceQty;

    /**
     *
     * @var \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
     */
    protected $getSalableQuantityDataBySku;

    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModel;

    /**
     *
     * @param \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku $source
     * @param \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $salableQty
     * @param \Magento\Catalog\Model\ProductFactory $productModel
     */
    public function __construct(
        \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku $source,
        \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $salableQty,
        \Magento\Catalog\Model\ProductFactory $productModel
    ) {
        $this->sourceQty = $source;
        $this->getSalableQuantityDataBySku = $salableQty;
        $this->productModel = $productModel;
    }
    
    /**
     * Return source inventory array
     *
     * @return boolean
     */
    public function getSourceInventory(string $sku, $productId = null, $productType = null)
    {
        if ($productId && $productType == 'bundle') {
            return $this->getBundleProductStockStatus($productId);
        }

        if ($productId && $productType == 'configurable') {
            return $this->getConfigurableProductStockStatus($productId);
        }

        $inventory = $this->getSalableQuantityDataBySku->execute($sku);
        if (isset($inventory)) {
            return $this->isInStock($inventory);
        }
    }

    /**
     * Return bundle product stock status based on salable qty
     *
     * @param string $productId
     * @return boolean
     */
    public function getBundleProductStockStatus($productId)
    {
        $product = $this->productModel->create()->load($productId);
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );
        $inStock = true;
        foreach ($selectionCollection as $item) {
            if ($item->getIsDefault()) {
                $inventory = $this->getSalableQuantityDataBySku->execute($item->getSku());
                if (isset($inventory)) {
                    $inventory = reset($inventory);
                    $inventory['qty'] >= (float)$item->getSelectionQty() ? $inStock = true : $inStock = false;
                    break;
                }
            };
        }

        return $inStock;
    }

    /**
     * return configurable product stock status based on salable quantity
     *
     * @param string $productId
     * @return boolean
     */
    public function getConfigurableProductStockStatus($productId)
    {
        $product = $this->productModel->create()->load($productId);
        $children = $product->getTypeInstance()->getUsedProducts($product);
        $isInStock = true;
        foreach ($children as $child) {
            $isInStock = $this->getSalableQuantityDataBySku->execute($child->getSku());
        }
        return $isInStock;
    }

    /**
     * check product stock status
     *
     * @param array $inventory
     * @return boolean
     */
    public function isInStock($inventory)
    {
        if (isset($inventory)) {
            $inventory = reset($inventory);
            if ($inventory['qty'] > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
}
