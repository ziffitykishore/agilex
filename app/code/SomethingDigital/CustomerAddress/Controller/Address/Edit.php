<?php

namespace SomethingDigital\CustomerAddress\Controller\Address;

class Edit extends \Magento\Customer\Controller\Address\Edit
{
    public function execute()
    {
        $addressId = $this->getRequest()->getParam('id', false);
        $address = $this->_addressRepository->getById($addressId);
        $isReadOnly = $address->getCustomAttribute('is_read_only');
        if ($isReadOnly->getValue()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('form');
    }
}
