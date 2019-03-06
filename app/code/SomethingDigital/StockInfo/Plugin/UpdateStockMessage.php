<?php

namespace SomethingDigital\StockInfo\Plugin;

use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use SomethingDigital\StockInfo\Model\Product\Attribute\Source\SxInventoryStatus;

class UpdateStockMessage
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * Add custom messages to the cart based on product attribute "sx_inventory_status"
     *
     * @param StockStateProviderInterface $subject
     * @param \Magento\Framework\DataObject $result
     * @param StockItemInterface $stockItem
     * @param mixed $qty
     * @param mixed $summaryQty
     * @param mixed $origQty
     * @return \Magento\Framework\DataObject
     */
    public function afterCheckQuoteItemQty(StockStateProviderInterface $subject, $result, StockItemInterface $stockItem, $qty, $summaryQty, $origQty = 0)
    {
        $product = $this->productRepository->getById($stockItem->getProductId());
        $sxInventory = $product->getData('sx_inventory_status');
        if ($sxInventory == SxInventoryStatus::STATUS_ORDER_AS_NEEDED) {
            $result->setMessage(__('Direct from factory'));
        } elseif ($sxInventory == SxInventoryStatus::STATUS_STOCK) {
            if (($stockItem->getQty() - $summaryQty < 0)
                && $stockItem->getProductName()
                && ($stockItem->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NOTIFY)
            ) {
                $result->setMessage(__('Items will be back ordered'));
            }
        }
        return $result;
    }
}
