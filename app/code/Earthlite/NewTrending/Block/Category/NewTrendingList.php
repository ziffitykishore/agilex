<?php
declare(strict_types = 1);
namespace Earthlite\NewTrending\Block\Category;

use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Api\CategoryRepositoryInterfaceFactory;

/**
 * class NewTrendingList
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
     * @var CategoryRepositoryInterfaceFactory 
     */
    protected $categoryRepositoryInterfaceFactory;

    /**

     * NewTrendingList Constructor
     * 
     * @param Context $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Visibility $productVisibility
     * @param ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory
     * @param Registry $registry
     * @param CategoryRepositoryInterfaceFactory $categoryRepositoryInterfaceFactory
     * @param array $data
     */
    public function __construct(
        Context $context, 
        ProductCollectionFactory $productCollectionFactory, 
        Visibility $productVisibility, 
        ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory, 
        Registry $registry, 
        CategoryRepositoryInterfaceFactory $categoryRepositoryInterfaceFactory, 
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->productRepositoryInterfaceFactory = $productRepositoryInterfaceFactory;
        $this->coreregistry = $registry;
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
        return $this->getProductCollection($this->getChildCategories($this->getCategoryId()));
    }

    /**
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getUpdatedCollection($collection) 
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $collection->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        );
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
        if ($image && $image != 'no_selection') {
            $productImageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $image;
        }
        return $productImageUrl;
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
        return $categoryDetails->getAllChildren(true);
    }

    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    private function getCategory() 
    {
        return $this->coreregistry->registry('current_category');
    }
    
    /**
     * Get category 
     *
     * @return int|string|null
     */
    protected function getCategoryId() 
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
        return \Magento\Catalog\Model\Category::TREE_ROOT_ID;
    }
    
    /**
     * 
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getNewTrendingListForMenuItems() 
    {        
        if ($this->_getData('new_trending_category')) {
            $category = explode('/',$this->_getData('new_trending_category'));
            return $this->getProductCollection($this->getChildCategories($category[1]));
        }
    }

    /**
     * 
     * @param array $categoryIds
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection(array $categoryIds) 
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create()->addCategoriesFilter(['in' => $categoryIds]);
        return $this->getUpdatedCollection($collection);
    }

}
