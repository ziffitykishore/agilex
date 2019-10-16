<?php

namespace PartySupplies\Customer\Plugin\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;

class ReviewTab
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
     * @param \Magento\Review\Block\Adminhtml\ReviewTab $subject
     * @param boolean $result
     * @return boolean
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanShowTab(
        \Magento\Review\Block\Adminhtml\ReviewTab $subject,
        $result
    ) {
        if ($result !== null) {
            $customer = $this->customerRegistry->retrieve($result);

            if ($customer->getAccountType() === Constant::COMPANY) {
                $result = false;
            }
        }
        return $result;
    }
}
