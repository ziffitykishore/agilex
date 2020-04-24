<?php
declare(strict_types=1);

namespace Earthlite\Category\Block\Category;

use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Category;
use Earthlite\Category\Model\ResourceModel\CategoryGallery\CollectionFactory as CatalogGalleryCollectionFactory;
use Magento\Store\Model\ScopeInterface;

/*
 * class View
 */
class View extends \Magento\Catalog\Block\Category\View
{
    const XML_PATH_CATEGORY_SLIDER = 'earthlite_category/category_general/slider_default_image';
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;
    
    /**
     *
     * @var CatalogGalleryCollectionFactory
     */
    protected $categoryGalleryCollectionFactory;

    /**
     * 
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        Category $categoryHelper,
        CatalogGalleryCollectionFactory $categoryGalleryCollectionFactory,
        array $data = []
    ) {
        $this->categoryGalleryCollectionFactory = $categoryGalleryCollectionFactory;
        parent::__construct($context,$layerResolver,$registry,$categoryHelper,$data);
    }

     /**
      * 
      * @return array
      */
     public function getImages()
     {
        return $this->categoryGalleryCollectionFactory->create()
                ->addFieldToFilter('category_id', $this->getCurrentCategory()->getId())
                ->setOrder('position','ASC');
     }
     
     /**
      * 
      * @return string
      */
     public function getMediaUrl() 
     {
         return $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
     }
     
     /**
      * 
      * @return string
      */
     public function getDefaultCategoryImage()
     {
         return $this->_scopeConfig->getValue(self::XML_PATH_CATEGORY_SLIDER, ScopeInterface::SCOPE_STORE);
     }
}
