<?php

namespace Ziffity\Inventory\Block;

use Magento\Catalog\Block\Product\View;

class SourceStock extends View 
{
    /**
     *
     * @var \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku
     */
    protected $sourceQty;

    /**
     * 
     * @param \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku $source
     */
    public function __construct(
        \Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku $source
    ) {
        $this->sourceQty = $source;
    }
    
    /**
     * Return source inventory array
     * 
     * @return array
     */
    public function getSourceInventory(string $sku)
    {
        $sourceItemsBySku = $this->sourceQty->execute($sku);
        $productInventory = [];
        foreach ($sourceItemsBySku as $sourceItem) {
            $productInventory[$sourceItem->getSourceCode()] = $sourceItem->getQuantity();
        }

        return $productInventory;
    }
}
