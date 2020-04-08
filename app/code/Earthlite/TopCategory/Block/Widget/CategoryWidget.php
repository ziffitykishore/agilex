<?php

namespace Earthlite\TopCategory\Block\Widget;

use Magento\Customer\Model\Context;


class CategoryWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    // @codingStandardsIgnoreStart
    protected $_categoryFactory;
    protected $httpContext;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Subcategory config path
     */
    const XML_PATH_TOPCATEGORY_CONFIG = 'subcategory/general/enabletopcategory';

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Catalog\Model\CategoryFactory $categoryFactory,
    \Magento\Framework\App\Http\Context $httpContext,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->httpContext = $httpContext;
        $this->_storeManager = $context->getStoreManager();
        $this->setTemplate($this->_getData('template'));
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function getCategorymodel($catid) 
    {
        $_category = $this->_categoryFactory->create();
        $_category->load($catid);
        return $_category;
    }

    public function getCatalogData() 
    {
        $catIds = explode(',', $this->_getData('parentcat'));
        $catIdsArray = array();
        if (isset($catIds)) {
            foreach ($catIds as $key => $values) {
                $catData = $this->getCategorymodel($values);
                $catIdsArray[$key] = $catData;
            }
        }
        $currentStore = $this->_storeManager->getStore();
        $mediaBaseUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $returndata = array(
            'catalogData' => $catIdsArray,
            'mediaBaseUrl' => $mediaBaseUrl
        );
        return $returndata;
    }    
    
    public function getTitle()
    {
        return $this->_getData('title');
    }

    public function getTopCategoryConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_TOPCATEGORY_CONFIG, $storeScope);
    }    
}
