<?php

namespace SomethingDigital\CustomerAddress\Plugin;

use Magento\Customer\Controller\Address\Delete;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Controller\Result\RedirectFactory;

class DeleteAddress
{
    protected $addressRepository;
    protected $resultRedirectFactory;

    /**
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->addressRepository = $addressRepository;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function aroundExecute(Delete $subject, callable $proceed)
    {
        $addressId = $subject->getRequest()->getParam('id', false);
        $address = $this->addressRepository->getById($addressId);
        $isReadOnly = $address->getCustomAttribute('is_read_only');
        $isBilling = $address->getCustomAttribute('is_billing');
        if ((isset($isReadOnly) && $isReadOnly->getValue() == "1") ||
            (isset($isBilling) && $isBilling->getValue() == "1")) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        return $proceed();
    }
}
