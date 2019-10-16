<?php

namespace PartySupplies\Customer\Plugin\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;

class WishlistTab
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
     * @param \Magento\Wishlist\Block\Adminhtml\WishlistTab $subject
     * @param boolean $result
     * @return boolean
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanShowTab(
        \Magento\Wishlist\Block\Adminhtml\WishlistTab $subject,
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
