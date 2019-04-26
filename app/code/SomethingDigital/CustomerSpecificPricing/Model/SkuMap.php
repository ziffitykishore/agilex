<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;


class SkuMap
{
    /**
     * @var LinkManagementInterface
     */
    private $linkManagement;

    public function __construct(
        LinkManagementInterface $linkManagement
    ) {
        $this->linkManagement = $linkManagement;
    }

    /**
     * Returns mappings for a product
     *
     * @param ProductInterface $product
     * @return string[]
     */
    public function getMap(ProductInterface $product)
    {
        $productType = $product->getTypeId();
        if ($productType == Configurable::TYPE_CODE) {
            return $this->getConfigurableMapping($product);
        } else {
            return '';
        }
    }

    /**
     * Creates a map of magento id to nourison skus 
     *
     * @param ProductInterface $product
     * @return string[]
     */
    private function getConfigurableMapping(ProductInterface $product)
    {
        /** @var string[] $map */
        $map = [];

        $map[$product->getId()] = $product->getSku();

        /** @var \Magento\Catalog\Api\Data\ProductInterface[] $childProducts */
        $childProducts = $this->linkManagement->getChildren($product->getSku());

        /** @var ProductInterface $child */
        foreach ($childProducts as $child) {
            $map[$child->getId()] = $child->getSku();
        }
        return $map;
    }
}
