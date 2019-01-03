<?php

namespace SomethingDigital\CustomPdp\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;

class BasePrice
{
   /**
    * @var SearchCriteriaBuilder
    */
    private $searchCriteriaBuilder;

   /**
    * @var ProductRepositoryInterface
    */
    private $productRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $groupBuilder;

    /**
     * @var LinkManagementInterface
     */
    private $linkManagement;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $groupBuilder,
        LinkManagementInterface $linkManagement
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->filterBuilder = $filterBuilder;
        $this->groupBuilder = $groupBuilder;
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
        } else if ($productType == Grouped::TYPE_CODE) {
            return $this->getGroupedPrice($product);
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
        $cheapestPrice = $childProducts[0]->getPrice();
        /** @var ProductInterface $child */
        foreach ($childProducts as $child) {
            $currentPrice = $child->getPrice();
            if ($currentPrice < $cheapestPrice) {
                $cheapestPrice = $currentPrice;
            }
        }
        return $cheapestPrice;
    }

    /**
     * @param ProductInterface $product
     * @return int|null
     */
    private function getGroupedPrice(ProductInterface $product)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface[] $children */
        $childProducts = $this->productHelper->getGroupedAssociatedProducts($product);
        if (count($childProducts) < 1) {
            return null;
        }
        /** @var int $cheapestPrice */
        $cheapestPrice = $childProducts[0]->getPrice();
        /** @var ProductInterface $child */
        foreach ($childProducts as $child) {
            $currentPrice = $child->getPrice();
            if ($currentPrice !== null && $currentPrice < $cheapestPrice) {
                $cheapestPrice = $currentPrice;
            }
        }
        return $cheapestPrice;
    }
}

