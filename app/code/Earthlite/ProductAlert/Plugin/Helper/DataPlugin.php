<?php
declare(strict_types = 1);
namespace Earthlite\ProductAlert\Plugin\Helper;

use Magento\ConfigurableProduct\Helper\Data;

/**
 * Class DataPlugin
 */
class DataPlugin
{
    /**
     * 
     * @param Data $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function aroundGetOptions(
        Data $subject, 
        callable $proceed,
        $currentProduct, 
        $allowedProducts
    ) {
        $options = [];
        $allowAttributes = $subject->getAllowAttributes($currentProduct);
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }
        return $options;
    }
    
    
}
