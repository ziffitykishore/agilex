<?php

namespace SomethingDigital\CheckoutShippingInfo\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface;

/**
 * Class CustomFields
 *
 * @category Model/Data
 * @package  Bodak\CheckoutCustomForm\Model\Data
 */
class CustomFields extends AbstractExtensibleObject implements CustomFieldsInterface
{
    /**
     * Get checkout PO Number
     *
     * @return string|null
     */
    public function getCheckoutPonumber()
    {
        return $this->_get(self::CHECKOUT_PONUMBER);
    }

    /**
     * Get checkout Shipto PO
     *
     * @return string|null
     */
    public function getCheckoutShiptopo()
    {
        return $this->_get(self::CHECKOUT_SHIPTOPO);
    }

    /**
     * Get checkout Delivery Point
     *
     * @return string|null
     */
    public function getCheckoutDeliverypoint()
    {
        return $this->_get(self::CHECKOUT_DELIVERYPOINT);
    }

    /**
     * Get checkout order notes
     *
     * @return string|null
     */
    public function getCheckoutOrdernotes()
    {
        return $this->_get(self::CHECKOUT_ORDERNOTES);
    }

    /**
     * Set checkout PO Number
     *
     * @param string|null $checkoutPonumber
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutPonumber(string $checkoutPonumber = null)
    {
        return $this->setData(self::CHECKOUT_PONUMBER, $checkoutPonumber);
    }

    /**
     * Set checkout Shipto PO
     *
     * @param string|null $checkoutShiptopo
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutShiptopo(string $checkoutShiptopo = null)
    {
        return $this->setData(self::CHECKOUT_SHIPTOPO, $checkoutShiptopo);
    }

    /**
     * Set checkout Delivery Point
     *
     * @param string|null $checkoutDeliverypoint
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutDeliverypoint(string $checkoutDeliverypoint = null)
    {
        return $this->setData(self::CHECKOUT_DELIVERYPOINT, $checkoutDeliverypoint);
    }

    /**
     * Set checkout order notes
     *
     * @param string|null $checkoutOrdernotes
     *
     * @return CustomFieldsInterface
     */
    public function setCheckoutOrdernotes(string $checkoutOrdernotes = null)
    {
        return $this->setData(self::CHECKOUT_ORDERNOTES, $checkoutOrdernotes);
    }

}
