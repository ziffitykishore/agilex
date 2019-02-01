<?php

namespace SomethingDigital\BryantPark\Helper;

class CustomHeaderLinks extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $moduleConfigPath;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->moduleConfigPath = 'design/headerlinks/';
    }

    public function getConfig($config_path)
    {
        $store = $this->_storeManager->getStore()->getId();

        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAboutLink()
    {
        return $this->getConfig($this->moduleConfigPath . 'contact_link');
    }

    public function getContactLink()
    {
        return $this->getConfig($this->moduleConfigPath . 'about_link');
    }
}
