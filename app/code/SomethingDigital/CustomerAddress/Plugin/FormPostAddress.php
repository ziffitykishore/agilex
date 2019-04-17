<?php

namespace SomethingDigital\CustomerAddress\Plugin;

use Magento\Customer\Controller\Address\FormPost;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Controller\Result\RedirectFactory;

class FormPostAddress
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

    public function aroundExecute(FormPost $subject, callable $proceed)
    {
        $addressId = $subject->getRequest()->getParam('id', false);
        if ($addressId) {
            $address = $this->addressRepository->getById($addressId);
            $isReadOnly = $address->getCustomAttribute('is_read_only');
            if ($isReadOnly->getValue()) {
                return $this->resultRedirectFactory->create()->setPath('*/*/index');
            }
        }

        return $proceed();
    }
}