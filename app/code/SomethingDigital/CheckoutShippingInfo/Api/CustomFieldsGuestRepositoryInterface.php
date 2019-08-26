<?php

namespace SomethingDigital\CheckoutShippingInfo\Api;

use Magento\Sales\Model\Order;
use SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface;

/**
 * Interface CustomFieldsGuestRepositoryInterface
 *
 * @category Api/Interface
 * @package  Bodak\CheckoutCustomForm\Api
 */
interface CustomFieldsGuestRepositoryInterface
{
    /**
     * Save checkout custom fields
     *
     * @param string                                                   $cartId       Guest Cart id
     * @param \SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface $customFields Custom fields
     *
     * @return \SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface
     */
    public function saveCustomFields(
        string $cartId,
        CustomFieldsInterface $customFields
    ): CustomFieldsInterface;
}
