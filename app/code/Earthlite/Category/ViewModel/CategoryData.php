<?php

namespace Earthlite\Category\ViewModel;

/**
 * class CategoryData
 */
class CategoryData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Helper
     *
     * @var \Magento\Framework\Registry
     */
    protected $_catalogHelper;

    /**
     * Category Model
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $_categoryModel;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * Recipient email config path
     */
    const XML_PATH_SUBCATEGORY_CONFIG = 'subcategory/general/enable';


    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Output $catalogHelper,
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_coreRegistry = $registry;
        $this->_catalogHelper = $catalogHelper;
        $this->_categoryModel = $categoryModel;
        $this->scopeConfig = $scopeConfig;
    }

    public function getCurrentCategory()
    {
        
        $category = $this->_coreRegistry->registry('current_category');
        
        return $category;
    }

    public function getCatalogHelper()
    {
        return $this->_catalogHelper;
    }

    public function loadCategoryById($id)
    {
        $category = $this->_categoryModel->load($id);
        
        return $category;
    }

    public function getSubcategoryConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_SUBCATEGORY_CONFIG, $storeScope);
    }
}
