<?php

namespace SomethingDigital\BryantPark\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Store\Model\ScopeInterface;

class Social extends AbstractHelper
{
    protected $scopeConfig;
    protected $moduleConfigPath;

    public function __construct(
        HelperContext $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->moduleConfigPath = 'design/socialprofiles/';
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE,
            null
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

    public function getYoutube()
    {
        return $this->getConfig($this->moduleConfigPath . 'social_youtube');
    }
}
