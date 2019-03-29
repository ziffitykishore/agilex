<?php

namespace SomethingDigital\CustomerAddress\Controller\Address;
 
class Delete extends \Magento\Customer\Controller\Address\Delete
{
    public function execute()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId && $this->_formKeyValidator->validate($this->getRequest())) {
            try {
                $address = $this->_addressRepository->getById($addressId);
                $isReadOnly = $address->getCustomAttribute('is_read_only');
                if ($isReadOnly->getValue()) {
                    return $this->resultRedirectFactory->create()->setPath('*/*/index');
                }
                if ($address->getCustomerId() === $this->_getSession()->getCustomerId()) {
                    $this->_addressRepository->deleteById($addressId);
                    $this->messageManager->addSuccess(__('You deleted the address.'));
                } else {
                    $this->messageManager->addError(__('We can\'t delete the address right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addException($other, __('We can\'t delete the address right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}