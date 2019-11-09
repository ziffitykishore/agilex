<?php

namespace PartySupplies\QuickOrder\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Item
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
     * To add MinSaleQty
     *
     * @param \Mageplaza\QuickOrder\Helper\Item $subject
     * @param array $result
     * @return array
     */
    public function afterGetPreItemDataArray(
        \Mageplaza\QuickOrder\Helper\Item $subject,
        $result
    ) {
        $product = $this->productRepository->getById($result['product_id']);

        $stockItem = $this->stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );

        $result['minSaleQty'] = $stockItem->getMinSaleQty();

        return $result;
    }
    
    /**
     * To add MinSaleQty
     *
     * @param \Mageplaza\QuickOrder\Helper\Item $subject
     * @param array $result
     * @return type
     */
    public function afterGetPreItemNotMeetConditionsFilter(
        \Mageplaza\QuickOrder\Helper\Item $subject,
        $result    
    ) {
        $product = $this->productRepository->getById($result['product_id']);

        $stockItem = $this->stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );

        $result['minSaleQty'] = $stockItem->getMinSaleQty();

        return $result;
    }
}
