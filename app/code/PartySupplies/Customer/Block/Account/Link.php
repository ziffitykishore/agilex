<?php

namespace PartySupplies\Customer\Block\Account;

use PartySupplies\Customer\Helper\Constant as CustomerHelper;

class Link extends \Magento\Customer\Block\Account\Link
{
    protected $customerModel;
    
    /**
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\Customer $customerModel,
        array $data = []
    ) {
        $this->customerModel = $customerModel;
        $this->httpContext = $httpContext;
        parent::__construct($context, $customerUrl, $data);
    }

    public function getLabel() {
        $customerId = $this->httpContext->getValue(CustomerHelper::CONTEXT_CUSTOMER_ID);
        $customer = $this->customerModel->load($customerId);
        $firstName = $customer->getData('firstname');
        return $firstName;
    }
}