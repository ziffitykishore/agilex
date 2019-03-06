<?php

namespace SomethingDigital\StockInfo\Model;

use Magento\Framework\Registry as CoreRegistry;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class StockData
{

    /**
     * @var CoreRegistry
     */
    private $coreRegistry;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        CoreRegistry $coreRegistry,
        ProductHelper $productHelper,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->productHelper = $productHelper;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Get product type
     *
     * @return string
     */
    public function getProductType()
    {
        return $this->getProduct()->getTypeId();
    }

    /**
     * Get stock data for warehouses
     *
     * Provide data for different product types
     *
     * @return []
     */
    public function getStockData()
    {
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }
        $products = [];
        if ($product->getTypeId() == 'configurable') {
            $skipSaleableCheck = $this->productHelper->getSkipSaleableCheck();
            $allProducts = $product->getTypeInstance()->getUsedProducts($product, null);
            foreach ($allProducts as $simpleProduct) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $simpleProduct;
                }
            }
        } elseif ($product->getTypeId() == 'bundle' || $product->getTypeId() == 'grouped') {
            $allProductIdsGrouped = $product->getTypeInstance()->getChildrenIds($product->getId(), false);
            $allProductIds = [];
            foreach ($allProductIdsGrouped as $group => $simpleProductIds) {
                foreach ($simpleProductIds as $simpleProductId) {
                    $allProductIds[] = $simpleProductId;
                }
            }
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('entity_id', $allProductIds, 'in')
                ->create();
            $result = $this->productRepository->getList($searchCriteria);
            foreach ($result->getItems() as $simpleProduct) {
                $products[] = $simpleProduct;
            }
        } else {
            $products = [$this->getProduct()];
        }
        return $this->prepareProductsStockData($products);
    }

    /**
     * Retrieve stock data for given products
     *
     * @param \Magento\Catalog\Model\Product[] $products
     * @return []
     */
    private function prepareProductsStockData($products)
    {
        if (count($products) == 0) {
            return [];
        }
        $stockData = [];
        foreach ($products as $product) {
            $stockData[$product->getId()] = $this->retrieveProductStockData($product);
        }
        return $stockData;
    }

    /**
     * Collect custom stock info from product
     *
     * This include In Stock data only
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return []
     */
    private function retrieveProductStockData($product)
    {
        $stock = [];
        if ($product->getData('wh_ca_status')) {
            $stock[] = [
                'sku' => $product->getSku(),
                'label' => __('Chatsworth, CA'),
                'in_stock' => (bool)$product->getData('wh_ca_status') ? __('In Stock') : __('Out Of Stock'),
                'qty' => $product->getData('wh_ca_qty')
            ];
        }
        if ($product->getData('wh_ny_status')) {
            $stock[] = [
                'sku' => $product->getSku(),
                'label' => __('Queens, NY'),
                'in_stock' => (bool)$product->getData('wh_ny_status') ? __('In Stock') : __('Out Of Stock'),
                'qty' => $product->getData('wh_ny_qty')
            ];
        }
        if ($product->getData('wh_sc_status')) {
            $stock[] = [
                'sku' => $product->getSku(),
                'label' => __('Duncan, SC'),
                'in_stock' => (bool)$product->getData('wh_sc_status') ? __('In Stock') : __('Out Of Stock'),
                'qty' => $product->getData('wh_sc_qty')
            ];
        }
        return $stock;
    }
}
