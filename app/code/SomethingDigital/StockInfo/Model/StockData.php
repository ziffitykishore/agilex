<?php

namespace SomethingDigital\StockInfo\Model;

use Magento\Framework\Registry as CoreRegistry;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

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

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    public function __construct(
        CoreRegistry $coreRegistry,
        ProductHelper $productHelper,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        StockRegistryInterface $stockRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->productHelper = $productHelper;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Retrieve current product model
     *
     * @param string $sku
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($sku = null)
    {
        if ($sku) {
            try {
                return $this->productRepository->get($sku);
            } catch (\Exception $e) {
                $this->logger->error("SomethingDigital_StockInfo - QuickOrder: " . $e->getMessage());
                return null;
            }
        } else {
            return $this->coreRegistry->registry('product');
        }
    }

    /**
     * Get sx inventory status
     *
     * @return string
     */
    public function getSxInventoryStatus()
    {
        return $this->getProduct()->getData('sx_inventory_status');
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
     * @param string $sku
     * @return array
     */
    public function getStockData($sku = null)
    {
        $product = $this->getProduct($sku);
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
            $products = [$this->getProduct($sku)];
        }
        return $this->prepareProductsStockData($products);
    }

    /**
     * Retrieve stock data for given products
     *
     * @param \Magento\Catalog\Model\Product[] $products
     * @return array
     */
    private function prepareProductsStockData($products)
    {
        if (count($products) == 0) {
            return [];
        }
        $stockData = [];
        foreach ($products as $product) {
            $productStockData = $this->retrieveProductStockData($product);
            if ($productStockData) {
                $stockData[$product->getId()] = $productStockData;
            }
        }
        return $stockData;
    }

    /**
     * Collect custom stock info from product
     *
     * This include In Stock data only
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function retrieveProductStockData($product)
    {
        $stock = [];
        if ($product->getData('wh_sc_status')) {
            $stock[] = [
                'sku' => $product->getSku(),
                'label' => __('Duncan, SC'),
                'in_stock' => (bool)$product->getData('wh_sc_status') ? __('In Stock') : __('Out Of Stock'),
                'qty' => $product->getData('wh_sc_qty')
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
        
        if ($product->getData('wh_ca_status')) {
            $stock[] = [
                'sku' => $product->getSku(),
                'label' => __('Chatsworth, CA'),
                'in_stock' => (bool)$product->getData('wh_ca_status') ? __('In Stock') : __('Out Of Stock'),
                'qty' => $product->getData('wh_ca_qty')
            ];
        }
        return $stock;
    }

    /**
     * Get product min_sale_qty and qty_increments
     *
     * @param $sku
     * @return array
     */
    public function getMinSaleQtyAndIncrementsInfo($sku)
    {
        $messages = [];
        try {
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            if ($stockItem) {
                if ($stockItem->getMinSaleQty() > 1) {
                    $messages[] = __('You must buy at least %1 of these per purchase.', $stockItem->getMinSaleQty());
                }
                if ($stockItem->getQtyIncrements() > 1) {
                    $messages[] = __('Sold in increments of %1', $stockItem->getQtyIncrements());
                }
            }
        } catch (NoSuchEntityException $e) {
            //no action required
        }
        return $messages;
    }
}
