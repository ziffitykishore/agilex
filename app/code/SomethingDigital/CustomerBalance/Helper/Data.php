<?php

namespace SomethingDigital\CustomerBalance\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    private const CUSTOMER_LINK_MESSAGE = 'customer/account_link_status/sx_link_message';

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getCustomerLinkMessage()
    {
        $status_message = $this->scopeConfig->getValue(self::CUSTOMER_LINK_MESSAGE);
        if (!empty($status_message)) {
            return $status_message;
        }
        
        return false;
    }

}