<?php

namespace SomethingDigital\CheckoutShippingInfo\Api\Data;

/**
 * Interface CustomFieldsInterface
 *
 * @category Api/Data/Interface
 * @package  Bodak\CheckoutCustomForm\Api\Data
 */
interface CustomFieldsInterface
{
    const CHECKOUT_PONUMBER = 'checkout_ponumber';
    const CHECKOUT_SHIPTOPO = 'checkout_shiptopo';
    const CHECKOUT_DELIVERYPOINT = 'checkout_deliverypoint';
    const CHECKOUT_ORDERNOTES = 'checkout_ordernotes';

    /**
     * Get checkout PO number
     *
     * @return string|null
     */
    public function getCheckoutPonumber();

    /**
     * Get checkout shipto PO
     *
     * @return string|null
     */
    public function getCheckoutShiptopo();

    /**
     * Get checkout Delivery Point
     *
     * @return string|null
     */
    public function getCheckoutDeliverypoint();

    /**
     * Get checkout Order Notes
     *
     * @return string|null
     */
    public function getCheckoutOrdernotes();

    /**
     * Set checkout PO number
     *
     * @param string|null $checkoutPonumber
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutPonumber(string $checkoutPonumber = null);

    /**
     * Set checkout shipto PO
     *
     * @param string|null $checkoutShiptopo
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutShiptopo(string $checkoutShiptopo = null);

    /**
     * Set checkout Delivery Point
     *
     * @param string|null $checkoutDeliverypoint
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutDeliverypoint(string $checkoutDeliverypoint = null);

    /**
     * Set checkout Order Notes
     *
     * @param string|null $checkoutOrdernotes
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutOrdernotes(string $checkoutOrdernotes = null);

}
