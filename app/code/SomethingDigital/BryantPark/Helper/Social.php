<?php

namespace SomethingDigital\BryantPark\Helper;
 
class Social extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_PATH = 'design/socialprofiles/';

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
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
        return $this->getConfig(CONFIG_PATH + 'social_facebook');
    }

    public function getTwitter()
    {
        return $this->getConfig(CONFIG_PATH + 'social_twitter');
    }

    public function getInstagram()
    {
        return $this->getConfig(CONFIG_PATH + 'social_instagram');
    }

    public function getPinterest()
    {
        $this->getConfig(CONFIG_PATH + 'social_pinterest');
    }

    public function getGooglePlus()
    {
        $this->getConfig(CONFIG_PATH + 'social_googleplus');
    }

    public function getSnapchat()
    {
        $this->getConfig(CONFIG_PATH + 'social_snapchat');
    }

    public function getHouzz()
    {
        $this->getConfig(CONFIG_PATH + 'social_houzz');
    }

    public function getLinkedIn()
    {
        $this->getConfig(CONFIG_PATH + 'social_linkedin');
    }
}
