<?php

namespace Earthlite\TopCategory\Block\Widget;

use Magento\Customer\Model\Context;
use \Earthlite\CustomerSession\Model\Customer\Context as CustomerIdContext;

class CategoryWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface {

    // @codingStandardsIgnoreStart
    protected $_categoryFactory;
    protected $httpContext;
    // protected $customerHelper;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Catalog\Model\CategoryFactory $categoryFactory,
    \Magento\Framework\App\Http\Context $httpContext
    // \Earthlite\Customer\Helper\Data $customerHelper
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->httpContext = $httpContext;
        // $this->_customerHelper = $customerHelper;
        $this->_storeManager = $context->getStoreManager();
        $this->setTemplate($this->_getData('template'));
        parent::__construct($context);
    }

    public function getCategorymodel($catid) {
        $_category = $this->_categoryFactory->create();
        $_category->load($catid);
        return $_category;
    }

    public function getCatalogData() {
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
    public function isLoggedIn() {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function getCustomerId() {
        return $this->httpContext->getValue(CustomerIdContext::CONTEXT_CUSTOMER_ID);
    }

    public function getUser(){
        $user = $this->_getData('user');
        $isB2BCustomer = null;
        if($this->isLoggedIn())
        {
            $isB2BCustomer = $this->_customerHelper->isBusinessCustomer($this->getCustomerId());
        }
        if($user == 'b2b_user')
        {
                return ($isB2BCustomer ? true : false);
        }else if($user == 'guest_b2c'){
            return ($isB2BCustomer ? false : true);
        }
        
        return true;
    }
    
    public function getTitle(){
        return $this->_getData('title');
    }
    
}
