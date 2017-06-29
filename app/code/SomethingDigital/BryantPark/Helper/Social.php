<?php

namespace SomethingDigital\BryantPark\Helper;
 
class Social extends \Magento\Framework\App\Helper\AbstractHelper
{
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

    public function getFacebook()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_facebook');
    }

    public function getTwitter()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_twitter');
    }

    public function getInstagram()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_instagram');
    }

    public function getPinterest()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_pinterest');
    }

    public function getGooglePlus()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_googleplus');
    }

    public function getSnapchat()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_snapchat');
    }

    public function getHouzz()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_houzz');
    }

    public function getLinkedIn()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_linkedin');
    }
}
