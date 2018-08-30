<?php

namespace Mconnect\Ajaxlogin\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;

/**
 * Search Suite Autocomplete config data helper
 * @package Mconnect\Autocompletesearch\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $couponCollectionFactory;

    /**
     * XML config path getAjaxloginRedirect
     */
    const XML_PATH_AJAXLOGIN_REDIRECT = 'mconnect_ajaxlogin/general/redirect';

    public function __construct(
    CouponCollectionFactory $couponCollectionFactory, Context $context,\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getCouponCode()
    {
        $ruleId = $this->scopeConfig->getValue("mconnect_ajaxlogin/signup_offer/signup_promo_rules", 'store');
        $collection = $this->couponCollectionFactory->create();
        $collection->addFieldToFilter('rule_id', $ruleId);
        return $collection->getFirstItem()->getCode();
    }
    
    public function getBannerImage(){
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $ruleId = $this->scopeConfig->getValue("mconnect_ajaxlogin/banner/banner_id", 'store');
        return $mediaUrl.'mconnect/banner/'.$ruleId;
    }

    /**
     * getAjaxloginRedirect
     *     
     */
    public function getAjaxloginRedirect($storeId = null)
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_AJAXLOGIN_REDIRECT, ScopeInterface::SCOPE_STORE,
                $storeId
        );
    }
}