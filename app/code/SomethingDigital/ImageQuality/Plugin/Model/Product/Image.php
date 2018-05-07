<?php

namespace SomethingDigital\ImageQuality\Plugin\Model\Product;

class Image extends \Magento\Catalog\Model\Product\Image {

    protected $moduleConfigPath;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->moduleConfigPath = 'dev/image/';
    }

    public function getConfig($config_path)
    {
        $store = $this->_storeManager->getStore()->getId();

        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function imageQuality()
    {
        $quality = $this->getConfig($this->moduleConfigPath . 'sd_image_quality');

        if ($quality && $quality > 0) {
            return $quality;
        } else {
            return 80;
        }
    }

    public function beforeGetImageProcessor($subject)
    {
        $subject->setQuality($this->imageQuality());

        return [$subject];
    }
}