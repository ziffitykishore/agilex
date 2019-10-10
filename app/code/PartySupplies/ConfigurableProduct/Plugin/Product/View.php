<?php

namespace PartySupplies\ConfigurableProduct\Plugin\Product;

use Magento\CatalogInventory\Api\StockRegistryInterface;

class View
{
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     *
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        StockRegistryInterface $stockRegistry
    ) {
        $this->stockRegistry = $stockRegistry;
    }
    
    /**
     * To store value of minSaleQty of product
     *
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param json $result
     * @return json
     */
    public function afterGetJsonConfig(
        \Magento\Catalog\Block\Product\View $subject,
        $result
    ) {
        $jsonResult = json_decode($result, true);

        $product = $subject->getProduct();
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());

        $jsonResult['casePack'] = $stockItem->getMinSaleQty();

        $result = json_encode($jsonResult);

        return $result;
    }
}
