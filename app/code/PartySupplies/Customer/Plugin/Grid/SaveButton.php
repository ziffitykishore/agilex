<?php

namespace PartySupplies\Customer\Plugin\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\Response\RedirectInterface;

class SaveButton
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
     *
     * @param \Magento\Customer\Block\Adminhtml\Edit\SaveButton $subject
     * @param array $result
     * @return array
     */
    public function afterGetButtonData(
        \Magento\Customer\Block\Adminhtml\Edit\SaveButton $subject,
        $result
    ) {
        if ($subject->getCustomerId() !== null) {
            $customer = $this->customerRegistry->retrieve($subject->getCustomerId());

            if ($customer->getAccountType() === Constant::COMPANY) {
                $result = [
                    'label' => __('Save Company'),
                    'class' => 'save primary'
                ];
            } else {
                $result = [
                    'label' => __('Save User'),
                    'class' => 'save primary'
                ];
            }
        } else {
            $url = $this->redirect->getRefererUrl();
            if (strpos($url, 'account_type/company') !== false) {
                $result = [
                    'label' => __('Save Company'),
                    'class' => 'save primary'
                ];
            } else {
                $result = [
                    'label' => __('Save User'),
                    'class' => 'save primary'
                ];
            }
        }
        return $result;
    }
}
