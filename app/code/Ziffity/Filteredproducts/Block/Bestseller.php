<?php
// @codingStandardsIgnoreFile
namespace Ziffity\Filteredproducts\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class Bestseller extends \Magento\Catalog\Block\Product\ListProduct
{

    /**
     * Product collection model
     *
     * @var Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_collection;

    /**
     * Product collection model
     *
     * @var Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_productCollection;

    /**
     * Image helper
     *
     * @var Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Catalog Layer
     *
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Initialize
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->_collection = $collection;
        $this->_imageHelper = $imageHelper;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Get product collection
     */
    protected function getProducts()
    {
        $limit = $this->getProductSize();
        $sortby = 'rand()';
        $storeId = 1;
        $sqlQuery = "e.entity_id = aggregation.product_id";
        if ($storeId > 0) {
            $sqlQuery .= " AND aggregation.store_id={$storeId}";
        }
        $this->_collection->clear()->getSelect()->reset('where');
        $collection = $this->_collection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_saleable', 1, 'left')
//            ->addAttributeToFilter('status', 1);
            ->addAttributeToFilter('visibility', 4);
        if ($this->getSortbyCollection() == "product_name") {
            $sortby = "rand()";
        } else if ($this->getSortbyCollection() == "product_price") {
            $sortby = "price DESC";
        } else if ($this->getSortbyCollection() == "qty_ordered") {
            $sortby = "sold_quantity DESC";
        }
        $collection->getSelect()->joinRight(
            ['aggregation' => 'sales_bestsellers_aggregated_yearly'],
            $sqlQuery,
            ['SUM(aggregation.qty_ordered) AS sold_quantity']
        )->group('e.entity_id')->order($sortby)->limit($limit);
        $this->_productCollection = $collection;
        return $this->_productCollection;
    }

    /*
     * Load and return product collection 
     */
    public function getLoadedProductCollection()
    {
        return $this->getProducts();
    }

    /*
     * Get product toolbar
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('pager');
    }

    /*
     * Get grid mode
     */
    public function getMode()
    {
        return 'grid';
    }

    /**
     * Get image helper
     */
    public function getImageHelper()
    {
        return $this->_imageHelper;
    }
    
    /**
     * Get the configured sortby of section
     * @return int
     */
    public function getSortbyCollection()
    {
        return 'qty_ordered';
    }

    /**
     * Get the configured sortby of section
     * @return int
     */
    public function getProductSize(){
        return $this->getData('product_size') ? $this->getData('product_size') : 5;
    }
}
