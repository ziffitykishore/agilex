<?php

namespace PartySupplies\Common\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class QuantityData implements ArgumentInterface
{
    /**
     * @var Magento\CatalogInventory\Model\StockRegistry
     */
    protected $stockRegistry;
    
    /**
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(StockRegistryInterface $stockRegistry)
    {
        $this->stockRegistry = $stockRegistry;
    }
    
    /**
     * @param type $productId
     * @param type $websiteId
     * @param array $validators
     * @return JSON
     */
    public function getQuantityValidators($productId, $websiteId, array $validators = [])
    {
        $stockItem = $this->stockRegistry->getStockItem($productId, $websiteId);

        $params = [];
        $params['minAllowed']  = (float)$stockItem->getMinSaleQty();
        if ($stockItem->getMaxSaleQty()) {
            $params['maxAllowed'] = (float)$stockItem->getMaxSaleQty();
        }
        if ($stockItem->getQtyIncrements() > 0) {
            $params['qtyIncrements'] = (float)$stockItem->getQtyIncrements();
        }
        $validators['validate-item-quantity'] = $params;

        return json_encode($validators);
    }
}
