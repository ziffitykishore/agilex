<?php

namespace SomethingDigital\BryantPark\Helper;

class GoogleVerificationTag extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_GOOGLE_VERIFICATION_TAG = 'google/verification/verification_tag';


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

    public function getGoogleVerificationTag()
    {
        return $this->getConfig(self::XML_PATH_GOOGLE_VERIFICATION_TAG);
    }
}
