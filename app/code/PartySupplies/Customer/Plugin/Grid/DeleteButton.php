<?php

namespace PartySupplies\Customer\Plugin\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;

class DeleteButton
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
     * @param \Magento\Customer\Block\Adminhtml\Edit\DeleteButton $subject
     * @param array $result
     * @return array
     */
    public function afterGetButtonData(
        \Magento\Customer\Block\Adminhtml\Edit\DeleteButton $subject,
        $result
    ) {
        if ($subject->getCustomerId() !== null) {
            $customer = $this->customerRegistry->retrieve($subject->getCustomerId());

            $result = [
                'label' => __('deleteUser')
            ];
            if ($customer->getAccountType() === Constant::COMPANY) {
                $result = [
                    'label' => __('deleteCompany')
                ];
            }
        }

        return $result;
    }
}
