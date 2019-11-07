<?php
namespace PartySupplies\Customer\Plugin\Html;

use PartySupplies\Customer\Helper\Constant as CustomerHelper;

class Topmenu
{
    public function afterGetIdentities(\Magento\Theme\Block\Html\Topmenu $subject, $result)
    {
        return array_merge(
            $result, [$subject->httpContext->getValue(CustomerHelper::CONTEXT_CUSTOMER_ID)]
        );
    }
}