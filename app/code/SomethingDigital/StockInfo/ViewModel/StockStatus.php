<?php

namespace SomethingDigital\StockInfo\ViewModel;

use Magento\Framework\Registry as CoreRegistry;

use SomethingDigital\StockInfo\Model\Product\Attribute\Source\SxInventoryStatus;

class StockStatus implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var CoreRegistry
     */
    private $coreRegistry;

    /**
     * @var SxInventoryStatus
     */
    private $sxInventoryStatusSource;

    public function __construct(
        CoreRegistry $coreRegistry,
        SxInventoryStatus $sxInventoryStatusSource
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->sxInventoryStatusSource = $sxInventoryStatusSource;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Get custom product status
     *
     * @return string
     */
    public function getStockStatus()
    {
        $product = $this->getProduct();
        $sxInventoryStatus = $product->getData('sx_inventory_status');
        $stockItem = $product->getExtensionAttributes()->getStockItem();
        $statusLabel = '';
        if ($sxInventoryStatus == SxInventoryStatus::STATUS_DNR) {
            if ($stockItem->getQty() > 5) {
                $statusLabel = __('In Stock');
            } elseif ($stockItem->getQty() == 0) {
                $statusLabel = __('%1 is no longer available for purchase. For additional information, please contact one of our customer service representatives at 1-800-221-0270.', $product->getName());
            } else {
                $statusLabel = __('Limited Supplies');
            }
        } elseif ($sxInventoryStatus == SxInventoryStatus::STATUS_ORDER_AS_NEEDED) {
            if ($stockItem->getQty() > 0) {
                $statusLabel = __('In Stock');
            } else {
                $statusLabel = __('Ships from Mfr.');
            }
        } else {
            if ($stockItem->getQty() > 0) {
                $statusLabel = __('In Stock');
            } else {
                $statusLabel = __('On Temporary Backorder');
            }
        }
        return $statusLabel;
    }
}
