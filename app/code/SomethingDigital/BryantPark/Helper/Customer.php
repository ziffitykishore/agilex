<?php

namespace SomethingDigital\BryantPark\Helper;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CUSTOMER_NEWSLETTER = 'customer/create_account/subscribe_by_default';

    protected $moduleConfigPath;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->moduleConfigPath = 'design/socialprofiles/';
    }

    public function getConfig($config_path)
    {
        $store = $this->_storeManager->getStore()->getId();

        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewsletterDefault()
    {
        return $this->getConfig(self::XML_PATH_CUSTOMER_NEWSLETTER) ? 'checked="checked" ' : '';
    }
}
