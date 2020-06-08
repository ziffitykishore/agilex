<?php

namespace Earthlite\Stickylinks\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class StoreConfig extends AbstractHelper
{

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {        
        $this->scopeConfig = $scopeConfig;       
    }
   

    public function getConfigValue($fullPath)
    {        
        return $this->scopeConfig->getValue($fullPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
