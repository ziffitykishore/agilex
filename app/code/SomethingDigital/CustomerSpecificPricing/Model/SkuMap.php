<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;
use SomethingDigital\CustomerSpecificPricing\Helper\Data as ProductHelper;


class SkuMap
{
    /**
     * @var LinkManagementInterface
     */
    private $linkManagement;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    public function __construct(
        LinkManagementInterface $linkManagement,
        ProductHelper $productHelper
    ) {
        $this->linkManagement = $linkManagement;
        $this->productHelper = $productHelper;
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
        } else if ($productType == Grouped::TYPE_CODE) {
            return $this->getGroupedMapping($product);
        } else {
            return '';
        }
    }

    /**
     * Creates a map of magento id to magento skus 
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

    /**
     * @param ProductInterface $product
     * @return string[]
     */
    private function getGroupedMapping(ProductInterface $product)
    {
        /** @var string[] $map */
        $map = [];

        /** @var \Magento\Catalog\Api\Data\ProductInterface[] $children */
        $children = $this->productHelper->getGroupedAssociatedProducts($product);
        foreach ($children as $child) {
            $map[$child->getId()] = $child->getSku();
        }
        return $map;
    }
}
