<?php

namespace SomethingDigital\ShipperHqOptionCustomizations\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider implements ConfigProviderInterface
{
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $config = [];
        $carrierOptions = $this->scopeConfig->getValue('carriers/shipper/customer_carrier_options', ScopeInterface::SCOPE_STORE);

        $config['shipperhq_customer_carrier_options'] = array_map('trim', explode(',',$carrierOptions));

        return $config;
    }
}
