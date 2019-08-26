<?php

namespace SomethingDigital\CheckoutShippingInfo\Api;

use Magento\Sales\Model\Order;
use SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface;

/**
 * Interface CustomFieldsRepositoryInterface
 *
 * @category Api/Interface
 * @package  Bodak\CheckoutCustomForm\Api
 */
interface CustomFieldsRepositoryInterface
{
    /**
     * Save checkout custom fields
     *
     * @param int                                                      $cartId       Cart id
     * @param \SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface $customFields Custom fields
     *
     * @return \SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface
     */
    public function saveCustomFields(
        int $cartId,
        CustomFieldsInterface $customFields
    ): CustomFieldsInterface;

    /**
     * Get checkoug custom fields
     *
     * @param Order $order Order
     *
     * @return CustomFieldsInterface
     */
    public function getCustomFields(Order $order) : CustomFieldsInterface;
}
