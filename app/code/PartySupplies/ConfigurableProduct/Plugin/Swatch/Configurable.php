<?php

namespace PartySupplies\ConfigurableProduct\Plugin\Swatch;

use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Configurable
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     *
     * @param ProductRepository $productRepository
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        ProductRepository $productRepository,
        StockRegistryInterface $stockRegistry
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * To store values of custom attributes
     *
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param json $result
     * @return json
     */
    public function afterGetJsonSwatchConfig(
        \Magento\Swatches\Block\Product\Renderer\Configurable $subject,
        $result
    ) {
        $jsonResult = json_decode($result, true);

        foreach ($subject->getAllowProducts() as $simpleProduct) {

            $child = $this->productRepository->get($simpleProduct->getSku());
            $stockItem = $this->stockRegistry->getStockItem($child->getId(), $child->getStore()->getWebsiteId());

            $jsonResult['sku'][$simpleProduct->getId()] = $simpleProduct->getSku();
            $jsonResult['item_no'][$child->getId()] = $this->getAttributeValue($child, 'item_no');
            $jsonResult['case_height'][$child->getId()] = $this->getAttributeValue($child, 'case_height');
            $jsonResult['case_width'][$child->getId()] = $this->getAttributeValue($child, 'case_width');
            $jsonResult['case_length'][$child->getId()] = $this->getAttributeValue($child, 'case_length');
            $jsonResult['case_weight'][$child->getId()] = $this->getAttributeValue($child, 'case_weight');
            $jsonResult['case_pack'][$child->getId()] = $stockItem->getMinSaleQty();
        }

        $result = json_encode($jsonResult);

        return $result;
    }
    
    /**
     *
     * @param ProductRepository $product
     * @param string $attributeName
     * @return string
     */
    protected function getAttributeValue($product, $attributeName)
    {
        if ($product->getCustomAttribute($attributeName)) {
            return $product->getCustomAttribute($attributeName)->getValue();
        }
    }
}
