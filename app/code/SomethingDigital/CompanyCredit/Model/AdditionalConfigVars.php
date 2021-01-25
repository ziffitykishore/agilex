<?php

namespace SomethingDigital\CompanyCredit\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AdditionalConfigVars implements ConfigProviderInterface
{   
    public function __construct(ScopeConfigInterface $scopeConfig)
      {
          $this->_scopeConfig = $scopeConfig;
      }
    public function getConfig()
    {    
        $enable_var = $this->_scopeConfig->getValue("companycreditmessage/general/enable");
        $additionalVariables['credit_message'] = $enable_var;
        return $additionalVariables;
    }
}