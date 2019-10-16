<?php

namespace PartySupplies\Customer\Plugin\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;

class View
{
    
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     *
     * @param CustomerRegistry  $customerRegistry
     */
    public function __construct(
        CustomerRegistry $customerRegistry
    ) {
        $this->customerRegistry = $customerRegistry;
    }

    /**
     *
     * @param \Magento\Customer\Block\Adminhtml\Edit\Tab\View $subject
     * @param string $result
     * @return string
     */
    public function afterGetTabLabel(
        \Magento\Customer\Block\Adminhtml\Edit\Tab\View $subject,
        $result
    ) {
        $customerId = $subject->getCustomerId();
        if ($customerId !== null) {
            $customer = $this->customerRegistry->retrieve($customerId);
            $result = __('customerView');
            if ($customer->getAccountType() === Constant::COMPANY) {
                $result = __('companyView');
            }
        }
        return $result;
    }
    
    /**
     *
     * @param \Magento\Customer\Block\Adminhtml\Edit\Tab\View $subject
     * @param string $result
     * @return string
     */
    public function afterGetTabTitle(
        \Magento\Customer\Block\Adminhtml\Edit\Tab\View $subject,
        $result
    ) {
        $customerId = $subject->getCustomerId();
        if ($customerId !== null) {
            $customer = $this->customerRegistry->retrieve($customerId);
            $result = __('customerView');
            if ($customer->getAccountType() === Constant::COMPANY) {
                $result = __('companyView');
            }
        }
        return $result;
    }
}
