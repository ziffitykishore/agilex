<?php

namespace Ziffity\Common\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
 
    protected $httpContext;
 
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context);
    }
 
    /**
     * function to get customer id from context
     *
     * @return int customerId
     */
    public function getCustomerId() {
        return $this->httpContext->getValue('customer_id');
    }
}
