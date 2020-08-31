<?php

namespace SomethingDigital\StockInfo\Plugin;

use Magento\ProductAlert\Helper\Data;
use SomethingDigital\StockInfo\Model\Product\Attribute\Source\SxInventoryStatus;
use Magento\CatalogInventory\Model\Stock\Item;

class HideStockInfoAlert
{
    /**
     * @var StockItem
     */
    protected $stockItem;

    public function __construct(
        Item $stockItem
    ) {
        $this->stockItem = $stockItem;
    }

    public function afterIsStockAlertAllowed(Data $subject, $result)
    {
        if (!$result) {
            return $result;
        }
        $product = $subject->getProduct();
        if ($product) {
            $sxInventoryStatus = $product->getData('sx_inventory_status');
            $stockItem = $this->stockItem->load($product->getId(), 'product_id');

            if ($sxInventoryStatus == SxInventoryStatus::STATUS_DNR) {
                if ($stockItem->getQty() == 0) {
                    return false;
                }
            }
        }
       
        return $result;
    }
}
