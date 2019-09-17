<?php

namespace Creatuity\Nav\Model\Data\Processor;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\DataObject;

class InventoryDataProcessor implements DataProcessorInterface
{
    protected $stockRegistry;

    public function __construct(StockRegistryInterface $stockRegistry)
    {
        $this->stockRegistry = $stockRegistry;
    }

    public function process(DataObject $productData, DataObject $intermediateData)
    {
        $stockItem = $this->stockRegistry->getStockItem($productData->getId());

        if ($stockItem->getQty() === $intermediateData->getQty()
            && $stockItem->getIsInStock() === $intermediateData->getIsInStock()
        ) {
            return;
        }

        $stockItem
            ->setQty($intermediateData->getQty())
            ->setIsInStock($intermediateData->getIsInStock())
        ;

        $this->stockRegistry->updateStockItemBySku($productData->getSku(), $stockItem);
    }
}
