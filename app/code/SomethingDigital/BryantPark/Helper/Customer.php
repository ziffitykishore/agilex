<?php

namespace SomethingDigital\BryantPark\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Store\Model\ScopeInterface;

class Customer extends AbstractHelper
{
    const XML_PATH_CUSTOMER_NEWSLETTER = 'customer/create_account/subscribe_by_default';

    protected $scopeConfig;

    public function __construct(
        HelperContext $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE,
            null
        );
    }

    public function getNewsletterDefault()
    {
        return $this->getConfig(self::XML_PATH_CUSTOMER_NEWSLETTER) ? 'checked="checked" ' : '';
    }
}
