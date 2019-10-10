<?php

namespace PartySupplies\Customer\Plugin\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;

class OrderButton
{
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;
    
    /**
     *
     * @param CustomerRegistry $customerRegistry
     */
    public function __construct(
        CustomerRegistry $customerRegistry
    ) {
        $this->customerRegistry = $customerRegistry;
    }
    
    /**
     *
     * @param \Magento\Customer\Block\Adminhtml\Edit\OrderButton $subject
     * @param array $result
     * @return array
     */
    public function afterGetButtonData(
        \Magento\Customer\Block\Adminhtml\Edit\OrderButton $subject,
        $result
    ) {
        if ($subject->getCustomerId() !== null) {
            $customer = $this->customerRegistry->retrieve($subject->getCustomerId());

            if ($customer->getAccountType() === Constant::COMPANY) {
                $result = [];
            }
        }
        return $result;
    }
}
