<?php
declare(strict_types=1);

namespace Earthlite\NewTrending\Block\Category;

use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Api\CategoryRepositoryInterfaceFactory;

/**
 * class NewTrendingList
 *
 */
class NewTrendingList extends AbstractProduct implements BlockInterface
{
    const IMAGE_PLACE_HOLDER = 'placeholder/catalog/placeholder/image_placeholder';
    
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
     *
     * @var Registry 
     */
    protected $coreregistry;
    
    /**
     *
     * @var DateTime
     */
    protected $dateTime;
    
    /**
     *
     * @var CategoryRepositoryInterfaceFactory 
     */
    protected $categoryRepositoryInterfaceFactory;

    /**
     * NewTrendingList Constructor
     * 
     * @param Context $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Status $productStatus
     * @param Visibility $productVisibility
     * @param ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param CategoryRepositoryInterfaceFactory $categoryRepositoryInterfaceFactory
     * @param array $data
     */
    public function __construct(
        Context $context, 
        ProductCollectionFactory $productCollectionFactory,
        Status $productStatus,
        Visibility $productVisibility,
        ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory,
        Registry $registry,
        DateTime $dateTime,
        CategoryRepositoryInterfaceFactory $categoryRepositoryInterfaceFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->productRepositoryInterfaceFactory = $productRepositoryInterfaceFactory;
        $this->coreregistry = $registry;
        $this->dateTime = $dateTime;
        $this->categoryRepositoryInterfaceFactory = $categoryRepositoryInterfaceFactory;
        parent::__construct($context, $data);
    }
    
    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->_getData('title');
    }
    
    /**
     * 
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getNewTrendingList()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $categoryIds = explode(',', $this->getChildCategories($this->getCategoryId()));
        $collection = $this->productCollectionFactory->create()
                ->addCategoriesFilter(['in' => $categoryIds]);
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
        $fromDate = $this->dateTime->date($format = null, $input = $this->getFromDate());
        $toDate = $this->dateTime->date($format = null, $input = $this->getToDate());
        $collection = $this->_addProductAttributesAndPrices($collection)
                ->addStoreFilter()
                ->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
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
        $productImageUrl = $this->_imageHelper->getDefaultPlaceholderUrl('thumbnail');
        if ($image) {
            $productImageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$image;
        } 
        return $productImageUrl;
    }
    
    /**
     * 
     * @return string|null
     */
    protected function getFromDate()
    {
        return $this->_scopeConfig->getValue('earthlite_category/new_trending/from_date',ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * 
     * @return string|null
     */
    protected function getToDate()
    {
        return $this->_scopeConfig->getValue('earthlite_category/new_trending/to_date',ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * 
     * @param type $categoryId
     * @return string
     */
    protected function getChildCategories($categoryId)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $categoryRepository = $this->categoryRepositoryInterfaceFactory->create();
        $categoryDetails = $categoryRepository->get($categoryId, $storeId);
        return $categoryDetails->getChildren();
    }
    
    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getCategory()
    {
        return $this->coreregistry->registry('current_category');
    }

    /**
     * Get category id
     *
     * @return int|string|null
     */
    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
        return \Magento\Catalog\Model\Category::TREE_ROOT_ID;
    }
}