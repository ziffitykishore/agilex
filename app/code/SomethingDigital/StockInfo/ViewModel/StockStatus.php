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
        $stockItem = $product->getData('stock_data');
        $statusLabel = '';
        if ($sxInventoryStatus == SxInventoryStatus::STATUS_DNR) {
            if ($stockItem->getQty > 0) {
                $statusLabel = __('DNR');
            } else {
                $statusLabel = __('No longer available');
            }
        } elseif ($sxInventoryStatus == SxInventoryStatus::STATUS_ORDER_AS_NEEDED) {
            $statusLabel = __('Order as needed');
        } else {
            if ($product->isAvailable()) {
                $statusLabel = __('In Stock');
            } else {
                $statusLabel = __('Out of stock');
            }
        }
        return $statusLabel;
    }
}
