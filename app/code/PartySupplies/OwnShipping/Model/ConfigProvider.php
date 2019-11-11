<?php

namespace PartySupplies\OwnShipping\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * @var string
     */
    public $basePath = 'carriers/ownshipping/';
    
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }
    
    /**
     * @param string $value
     * @return mixed
     */
    public function getScopeValue($value)
    {
        return $this->scopeConfig->getValue($this->basePath.$value);
    }
    
    /**
     * To get configuration values.
     *
     * @return string
     */
    public function getConfig()
    {
        return [
            'ownShipping' => [
                'tooltip' => $this->getScopeValue('tooltip')
            ]
        ];
    }
}
