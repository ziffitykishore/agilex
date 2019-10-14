<?php

namespace PartySupplies\Customer\Plugin\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\Response\RedirectInterface;

class BackButton
{
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;
    
    /**
     * @var RedirectInterface
     */
    protected $redirect;
    
    /**
     *
     * @param CustomerRegistry  $customerRegistry
     * @param RedirectInterface $redirect
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        RedirectInterface $redirect
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->redirect = $redirect;
    }
    
    /**
     * Set url for back button in admin grid
     *
     * @param \Magento\Customer\Block\Adminhtml\Edit\BackButton $subject
     * @param array $result
     * @return array
     */
    public function afterGetButtonData(
        \Magento\Customer\Block\Adminhtml\Edit\BackButton $subject,
        $result
    ) {
        if ($subject->getCustomerId() !== null) {
            $customer = $this->customerRegistry->retrieve($subject->getCustomerId());

            $backUrl = $subject->getBackUrl().'account_type/customer';

            if ($customer->getAccountType() === Constant::COMPANY) {
                $backUrl = $subject->getBackUrl().'account_type/company';
            }
        } else {
            $backUrl = $subject->getBackUrl().'account_type/customer';

            if (strpos($this->redirect->getRefererUrl(), 'account_type/company') !== false) {
                $backUrl = $subject->getBackUrl().'account_type/company';
            }
        }

        $result = [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $backUrl),
            'class' => 'back',
            'sort_order' => 10
        ];

        return $result;
    }
}
