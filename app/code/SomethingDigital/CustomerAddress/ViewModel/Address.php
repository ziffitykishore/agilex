<?php

namespace SomethingDigital\CustomerAddress\ViewModel;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Address implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $addressRepository;
    
    public function __construct(
        AddressRepositoryInterface $addressRepository
    ) {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     */
    public function getAddressById($addressId)
    {
        try {
            return $this->addressRepository->getById($addressId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}