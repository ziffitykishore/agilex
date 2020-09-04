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
    public function isAddressIdReadOnly($addressId)
    {
        try {
            $address = $this->addressRepository->getById($addressId);
            if ($address->getId()) {
                $addressReadOnly = $address->getCustomAttribute('is_read_only');
                $addressIsBilling = $address->getCustomAttribute('is_billing');
                if (($addressReadOnly !== null && $addressReadOnly->getValue())
                    || ($addressIsBilling !== null && $addressIsBilling->getValue())
                ) {
                    return true;
                }
            }
        } catch (NoSuchEntityException $e) {
            // no action needed
        }
        return false;
    }
}