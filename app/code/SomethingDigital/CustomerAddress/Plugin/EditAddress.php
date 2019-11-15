<?php

namespace SomethingDigital\CustomerAddress\Plugin;

use Magento\Customer\Controller\Address\Edit;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Controller\Result\RedirectFactory;

class EditAddress
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

    public function aroundExecute(Edit $subject, callable $proceed)
    {
        $addressId = $subject->getRequest()->getParam('id', false);
        $address = $this->addressRepository->getById($addressId);
        $isReadOnly = $address->getCustomAttribute('is_read_only');
        if (!isset($isReadOnly) || !$isReadOnly->getValue()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        return $proceed();
    }
}