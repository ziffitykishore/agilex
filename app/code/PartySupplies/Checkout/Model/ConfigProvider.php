<?php

namespace PartySupplies\Checkout\Model;

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
    public $basePath = 'partysupplies_warehouse/general/';
    
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
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
     * {@inheritDoc}
     * @see \Magento\Checkout\Model\ConfigProviderInterface::getConfig()
     */
    public function getConfig()
    {
        return [
              'warehouse' => [
                  'branch_name' => $this->getScopeValue('branch_name'),
                  'street1' => $this->getScopeValue('street1'),
                  'street2' => $this->getScopeValue('street2'),
                  'city' => $this->getScopeValue('city'),
                  'state' => $this->getScopeValue('state'),
                  'country' => $this->getScopeValue('country'),
                  'zip_code' => $this->getScopeValue('zip_code'),
                  'phone' => $this->getScopeValue('phone')
              ]
            ];
    }
}
