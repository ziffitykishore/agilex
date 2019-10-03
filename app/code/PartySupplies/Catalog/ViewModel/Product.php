<?php

namespace PartySupplies\Catalog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Pricing\Helper\Data;

class Product implements ArgumentInterface
{
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;
    
    /**
     * @var Data
     */
    protected $priceHelper;

    /**
     *
     * @param StockRegistryInterface $stockRegistry
     * @param Data                   $priceHelper
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        Data $priceHelper
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->priceHelper = $priceHelper;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getMinQty($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $minSaleQty = $stockItem->getMinSaleQty();

        return $minSaleQty > 0 ? $minSaleQty : null;
    }
    
    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getCasePrice($product)
    {
        $formattedPrice = $this->priceHelper->currency(
            $product->getFinalPrice() * $this->getMinQty($product),
            true,
            false
        );

        return $formattedPrice;
    }
}
