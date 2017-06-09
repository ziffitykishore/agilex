<?php

namespace SomethingDigital\BryantPark\Helper;
 
class Social extends \Magento\Framework\App\Helper\AbstractHelper
{
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
        return $this->getConfig('socialprofiles/profiles/social_facebook');
    }

    public function getTwitter()
    {
        return $this->getConfig('socialprofiles/profiles/social_twitter');
    }

    public function getInstagram()
    {
        return $this->getConfig('socialprofiles/profiles/social_instagram');
    }

    public function getPinterest()
    {
        $this->getConfig('socialprofiles/profiles/social_pinterest');
    }

    public function getGooglePlus()
    {
        $this->getConfig('socialprofiles/profiles/social_googleplus');
    }

    public function getSnapchat()
    {
        $this->getConfig('socialprofiles/profiles/social_snapchat');
    }

    public function getHouzz()
    {
        $this->getConfig('socialprofiles/profiles/social_houzz');
    }

    public function getLinkedIn()
    {
        $this->getConfig('socialprofiles/profiles/social_linkedin');
    }
}
