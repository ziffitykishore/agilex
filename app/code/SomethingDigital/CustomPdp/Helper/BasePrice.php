<?php

namespace SomethingDigital\CustomPdp\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;

/**
 * Calculates base price of a product type
 */
class BasePrice
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
     * @param ProductInterface $product
     * @return int|null
     */
    public function getPrice(ProductInterface $product)
    {
        $productType = $product->getTypeId();
        if ($productType == 'simple') {
            return $this->getSimplePrice($product);
        } else if ($productType == Configurable::TYPE_CODE) {
            return $this->getConfigurablePrice($product);
        } 
    }

    /**
     * @param ProductInterface $product
     * @return int|null
     */
    private function getSimplePrice(ProductInterface $product)
    {
        return $product->getPrice();
    }

    /**
     * @param ProductInterface $product
     * @return int|null
     */
    private function getConfigurablePrice(ProductInterface $product)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface[] $childProducts */
        $childProducts = $this->linkManagement->getChildren($product->getSku());
        if (count($childProducts) < 1) {
            return null;
        }
        /** @var int $cheapestPrice */
        $cheapestPrice = $this->getCheapestPrice($childProducts);
        return $cheapestPrice;
    }

    /**
     * Calculates cheapest price in an array of products
     *
     * @param ProductInterface[] $products
     * @return int cheapest price
     */
    private function getCheapestPrice(array $products)
    {
        return array_reduce(
            $products,
            function ($cheapest, $product) {
                if ($cheapest === null) {
                    return $product->getPrice();
                }
                return min($cheapest, $product->getPrice());
            }
        );
    }
}

