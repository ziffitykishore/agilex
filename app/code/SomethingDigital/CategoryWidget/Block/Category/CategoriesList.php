<?php

namespace SomethingDigital\CategoryWidget\Block\Category;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\App\Http\Context;
use Magento\Catalog\Model\Category;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Widget\Block\BlockInterface;

/**
 * Class CategoriesList
 *
 * Catalog Categories List widget block
 *
 * @method CategoriesList setCategoryCollection(CategoryCollection $collection)
 * @method CategoryCollection getCategoryCollection()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 *
 * @package SomethingDigital\CategoryWidget\Block\Category
 */
class CategoriesList extends Template implements BlockInterface, IdentityInterface
{
    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    protected $json;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * CategoriesList constructor
     *
     * @param Context                   $httpContext
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Json                      $json
     * @param Template\Context          $context
     * @param array                     $data
     */
    public function __construct(
        Context $httpContext,
        CategoryCollectionFactory $categoryCollectionFactory,
        Json $json,
        Template\Context $context,
        array $data = []
    ) {
        $this->httpContext               = $httpContext;
        $this->json                      = $json;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        parent::__construct($context, $data);
    }//end __construct()

    /**
     * Get key pieces for caching block content
     *
     * @return array
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     * @throws NoSuchEntityException
     */
    public function getCacheKeyInfo()
    {
        return [
            'CATALOG_CATEGORIES_LIST_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            $this->getParentCategoryId(),
            $this->getCategoriesCount(),
            $this->json->serialize($this->getRequest()->getParams()),
            $this->getTemplate(),
            $this->getTitle()
        ];
    }//end getCacheKeyInfo()

    /**
     * Prepare and return category collection
     *
     * @return CategoryCollection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createCollection()
    {
        /** @var CategoryCollection $collection */
        $collection = $this->categoryCollectionFactory->create();

        $rootId = $this->_storeManager->getStore()->getRootCategoryId();

        $collection->setStoreId($this->_storeManager->getStore()->getId());
        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }

        $collection->addAttributeToSelect('*')->joinUrlRewrite();
        $collection->addFieldToFilter('path', ['like' => '1/' . $rootId . '/%']); // load only from store root
        $collection->addFieldToFilter('parent_id', $this->getParentCategoryId()); // load only direct child categories
        $collection->addAttributeToFilter('include_in_menu', 1); // load only category which can include into menu
        $collection->addIsActiveFilter(); //active category filter
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);

        if ($this->getPageSize()) {
            $collection->setPageSize($this->getPageSize());
        }

        return $collection;
    }//end createCollection()

    /**
     * Retrieve how many categories should be displayed
     *
     * @return mixed|null
     */
    public function getCategoriesCount()
    {
        if ($this->hasData('categories_count')) {
            return $this->getData('categories_count');
        }

        return null;
    }//end getCategoriesCount()

    /**
     * Retrieve parent category id
     *
     * @return bool|mixed
     */
    public function getParentCategoryId()
    {
        if ($this->hasData('parent_category_id')) {
            return $this->getData('parent_category_id');
        }

        return false;
    }//end getParentCategoryId()

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getCategoryCollection()) {
            foreach ($this->getCategoryCollection() as $category) {
                if ($category instanceof IdentityInterface) {
                    $identities = array_merge($identities, $category->getIdentities());
                }
            }
        }

        return $identities ?: [Category::CACHE_TAG];
    }//end getIdentities()

    /**
     * Get value of widget's title parameter
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }//end getTitle()

    /**
     * Internal constructor, that is called from real constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags'     => [
                Category::CACHE_TAG,
            ],
        ]);
    }//end _construct()

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->setCategoryCollection($this->createCollection());
        return parent::_beforeToHtml();
    }//end _beforeToHtml()

    /**
     * Retrieve how many categories should be displayed on page
     *
     * @return int
     */
    protected function getPageSize()
    {
        return $this->getCategoriesCount();
    }//end getPageSize()
}
