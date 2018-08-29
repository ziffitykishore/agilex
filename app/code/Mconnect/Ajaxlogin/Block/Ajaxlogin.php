<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Mconnect\Ajaxlogin\Block;

/**
 * Autocomplete class used to paste config data
 * @package Mconnect\Autocompletesearch\Block
 */
class Ajaxlogin extends \Magento\Customer\Block\Form\Login
{
    /**
     * Ajaxlogin constructor.      
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    /**
     * @var int
     */
    private $_username = -1;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    protected $_session;
    protected $_storeManager;
    protected $_urlInterface;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Customer\Model\Url $customerUrl,
    \Magento\Customer\Model\Session $session,
    \Mconnect\Ajaxlogin\Helper\Data $helperData, array $data = []
    )
    {
        parent::__construct($context, $customerSession, $customerUrl, $data);
        $this->_isScopePrivate  = false;
        $this->_customerUrl     = $customerUrl;
        $this->_customerSession = $customerSession;
        $this->_session         = $session;
        $this->helperData       = $helperData;
        $this->_storeManager    = $context->getStoreManager();
        $this->_urlInterface    = $context->getUrlBuilder();
    }

    public function isCustomerLoggedIn()
    {
        return $this->_session->isLoggedIn();
    }

    public function getAjaxUrl()
    {
        return $this->getUrl("customer/ajax/login");
    }

    public function getHomeUrl()
    {
        return $this->getUrl();
    }
    /*
     * getAjaxloginRedirect
     *
     *
     */

    public function getAjaxloginRedirect()
    {
        return $this->helperData->getAjaxloginRedirect();
    }
    /*
      public function getUrlInterfaceData()
      {
      return $this->_urlInterface->getCurrentUrl();

      //echo $this->_urlInterface->getUrl() . '<br />';

      // echo $this->_urlInterface->getUrl('test/data/22') . '<br />';

      // echo $this->_urlInterface->getBaseUrl() . '<br />';

      }
     */

    /*
     * getCurrentUrl 
     */

    public function getCurrentUrl()
    {
        return $this->_urlInterface->getCurrentUrl();
    }
    /*
      protected $_session;

      public function __construct(

      \Magento\Customer\Model\Session $session

      ) {

      $this->_session = $session;

      } */

    /*Overwrite for set the title default in every module */
    protected function _prepareLayout()
    {
        
        //$this->pageConfig->getTitle()->set(__('Create New '));
	//return _prepareLayout();
 
    }
    
    public function getBanner(){
        $image = $this->helperData->getBannerImage();
        return $image;
    }
}
