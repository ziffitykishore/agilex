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
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepos;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $helperImageFactory;

    /**
     * 
     * @param ProductRepository $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\View\Asset\Repository $assetRepos
     * @param \Magento\Catalog\Helper\ImageFactory $helperImageFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        StockRegistryInterface $stockRegistry,
        \Magento\Framework\View\Asset\Repository $assetRepos,
        \Magento\Catalog\Helper\ImageFactory $helperImageFactory
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
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

    /**
     * To add placeholder image.
     *
     * @param \Mageplaza\QuickOrder\Helper\Item $subject
     * @param string $result
     * @param string $skuChild
     * @param int $productId
     * @param int $store
     * @return string
     */
    public function afterGetProductImageUrl(
        \Mageplaza\QuickOrder\Helper\Item $subject,
        $result,
        $skuChild,
        $productId,
        $store
    ) {

        if ($skuChild != '') {
            $product = $this->_productRepository->get($skuChild);
        } else {
            $product = $this->productRepository->getById($productId);
        }

        if (!$product->getImage() || $product->getImage() == 'no_selection') {
            $imagePlaceholder = $this->helperImageFactory->create();
            $result = $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
        }

        return $result;
    }
}
