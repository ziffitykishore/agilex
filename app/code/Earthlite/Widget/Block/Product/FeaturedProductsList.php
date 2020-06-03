<?php
declare(strict_types=1);

namespace Earthlite\Widget\Block\Product;

use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class FeaturedProductsList
 *
 */
class FeaturedProductsList extends AbstractProduct implements BlockInterface
{
    const IMAGE_PLACE_HOLDER = 'placeholder/catalog/placeholder/image_placeholder';
    
    /**
     *
     * @var string
     */
    protected $_template = "widget/featured_product_list.phtml";
    
    /**
     *
     * @var ProductCollectionFactory 
     */
    protected $productCollectionFactory;
    
    /**
     *
     * @var Status 
     */
    protected $productStatus;
    
    /**
     *
     * @var Visibility 
     */
    protected $productVisibility;
    
    /**
     *
     * @var ProductRepositoryInterfaceFactory 
     */
    protected $productRepositoryInterfaceFactory;

    
    /**
     * FeaturedProductsList Constructor
     * 
     * @param Context $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Status $productStatus
     * @param Visibility $productVisibility
     * @param ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param array $data
     */
    public function __construct(
        Context $context, 
        ProductCollectionFactory $productCollectionFactory,
        Status $productStatus,
        Visibility $productVisibility,
        ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->productRepositoryInterfaceFactory = $productRepositoryInterfaceFactory;
        parent::__construct($context, $data);
    }
    
    /**
     * 
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getFeaturedProducts()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        return $this->getUpdatedCollection($collection);        
    }
    
    /**
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getUpdatedCollection($collection)
    {
        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }
        $collection->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
                ->addStoreFilter()
                ->addAttributeToFilter('is_featured', 1)
                ->addAttributeToSort('created_at', 'desc');
        $collection->distinct(true);

        return $collection;
    }

        /**
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProductDetails(int $productId) 
    {
        $product = $this->productRepositoryInterfaceFactory->create()->getById($productId);
        return $product;
    }

    /**
     * @param  $image
     * @return string
     */
    public function getImageUrl($image)
    {
        $productImageUrl = $this->_imageHelper->getDefaultPlaceholderUrl('image');
        if ($image && $image!='no_selection') {
            $productImageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$image;
        } 
        return $productImageUrl;
    }
    
}