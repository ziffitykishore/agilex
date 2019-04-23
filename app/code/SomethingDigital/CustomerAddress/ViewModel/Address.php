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
            $shippingAddress = $this->addressRepository->getById($addressId);
            if ($shippingAddress) {
                $shippingAddressReadOnly = $shippingAddress ? $shippingAddress->getCustomAttribute('is_read_only') : null;
                if ($shippingAddress !== null && ($shippingAddressReadOnly === null || !$shippingAddressReadOnly->getValue())) {
                    return false;
                } else {
                    return true;
                }
            }
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}