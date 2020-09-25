<?php

namespace SomethingDigital\CheckoutAddress\Plugin;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\AddressRepositoryInterface;

class SetIsBilling
{
    public function beforeSave(AddressRepositoryInterface $subject, AddressInterface $address)
    {
        if ($address->isDefaultBilling()) {
            $address->setCustomAttribute('is_billing', true);
        }
        return [$address];
    }
}
