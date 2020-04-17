<?php

namespace Earthlite\LayerNavigation\ViewModel;

class LayerData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Recipient layer navigation config path
     */
    const XML_PATH_LAYERNAVIGATION_CONFIG = 'layernavigation/general/enablelayernavigation';

    /**
     * Recipient Price SLider config path
     */
    const XML_PATH_PRICESLIDER_CONFIG = 'layernavigation/general/enablepriceslider';


    public function __construct(        
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }  

    public function getLayerNavigationConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_LAYERNAVIGATION_CONFIG, $storeScope);
    }

    public function getPriceSliderConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_PRICESLIDER_CONFIG, $storeScope);
    }
}
