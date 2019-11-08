<?php
namespace PartySupplies\Customer\Plugin\Html;

use PartySupplies\Customer\Helper\Constant as CustomerHelper;

class Topmenu
{
    protected $httpContext;
    
    public function __construct(\Magento\Framework\App\Http\Context $httpContext)
    {
        $this->httpContext = $httpContext;
    }

    public function afterGetIdentities(\Magento\Theme\Block\Html\Topmenu $subject, $result)
    {
        return array_merge(
            $result, [$this->httpContext->getValue(CustomerHelper::CONTEXT_CUSTOMER_ID)]
        );
    }
}