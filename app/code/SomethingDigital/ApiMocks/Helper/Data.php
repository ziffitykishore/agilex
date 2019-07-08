<?php

namespace SomethingDigital\ApiMocks\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_APIMOCKS_ENABLE = 'apimocks/general/enable';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $config;

    public function __construct(
        Context $context,
        ScopeConfigInterface $config
    ) {
        parent::__construct($context);
        $this->config = $config;
    }

    /** @return bool|string */
    public function isEnabled()
    {
        return $this->config->getValue(self::XML_PATH_APIMOCKS_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
