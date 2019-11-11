<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class OrderFieldOrderDataExtractor implements OrderDataExtractorInterface
{
    /**
     * UPS GROUND Shipping code
     */
    const UPS_GROUND = 'ups_03';

    /**
     * UPS NEXT DAY Shipping code
     */
    const UPS_NEXT_DAY = 'ups_01';

    /**
     * UPS SECOND DAY Shipping code
     */
    const UPS_SECOND_DAY = 'ups_02';

    /**
     * UPS THREE DAY Shipping code
     */
    const UPS_THREE_DAY = 'ups_12';

    /**
     * PALLET Shipping code
     */
    const PALLET = 'palletshipping_palletshipping';

    /**
     * OWN Shipping code.
     */
    const OWN_SHIPPING = 'ownshipping_ownshipping';

    /**
     * PayOnAccount payment code
     */
    const PAY_ON_ACCOUNT = 'payonaccount';

    /**
     * Authorize.net payment code
     */
    const AUTH_NET = 'authorizenet_directpost';

    /**
     * PayPal payment code
     */
    const PAYPAL = 'paypal_express';

    /**
     * @var string
     */
    protected $accessorMethod;

    /**
     *
     * @param string $accessorMethod
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        $accessorMethod,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->accessorMethod = $accessorMethod;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * To extract information from order
     *
     * @param OrderInterface $order
     * @return mixed
     */
    public function extract(OrderInterface $order)
    {
        return $this->{$this->accessorMethod}($order);
    }

    /**
     * To get shipping carrier code
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getShippingCarrier($order)
    {
        $shippingCode = $order->getShippingMethod();

        if ($shippingCode === self::UPS_GROUND
            || $shippingCode === self::UPS_NEXT_DAY
            || $shippingCode === self::UPS_SECOND_DAY
            || $shippingCode === self::UPS_THREE_DAY
        ) {
            return $this->scopeConfig->getValue(
                'nav/order_sync_shipping_data_provider/ups_shipping_agent_code',
                ScopeInterface::SCOPE_STORE
            );
        } elseif ($shippingCode === self::PALLET) {
            return $this->scopeConfig->getValue(
                'nav/order_sync_shipping_data_provider/pallet_shipping_agent_code',
                ScopeInterface::SCOPE_STORE
            );
        } elseif ($shippingCode === self::OWN_SHIPPING) {
            return $this->scopeConfig->getValue(
                'nav/order_sync_shipping_data_provider/own_shipping_agent_code',
                ScopeInterface::SCOPE_STORE
            );
        } else {
            $order->getShippingMethod();
        }
    }

    /**
     * To get shipping method code
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getShippingMethod($order)
    {
        $shippingCode = $order->getShippingMethod();

        switch ($shippingCode) {

            case self::UPS_GROUND:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_shipping_data_provider/upsgrnd_shipment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            case self::UPS_NEXT_DAY:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_shipping_data_provider/ups1_shipment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            case self::UPS_SECOND_DAY:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_shipping_data_provider/ups2_shipment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            case self::UPS_THREE_DAY:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_shipping_data_provider/ups3_shipment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            case self::PALLET:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_shipping_data_provider/pallet_shipment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            case self::OWN_SHIPPING:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_shipping_data_provider/own_shipment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            default:
                return $order->getShippingMethod();
        }
    }

    /**
     * To get payment method code
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getPaymentMethod($order)
    {
        $paymentMethod = $order->getPayment()->getMethod();

        switch ($paymentMethod) {

            case self::PAYPAL:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_payment_data_provider/paypal_payment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            case self::AUTH_NET:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_payment_data_provider/auth_net_payment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            case self::PAY_ON_ACCOUNT:
                return $this->scopeConfig->getValue(
                    'nav/order_sync_payment_data_provider/payonaccount_payment_method_code',
                    ScopeInterface::SCOPE_STORE
                );

            default:
                return $paymentMethod;
        }
    }


    /**
     * To get increment id from order
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getIncrementId($order)
    {
        return $order->getIncrementId();
    }

    /**
     * To get grand total from order
     *
     * @param OrderInterface $order
     * @return float
     */
    protected function getGrandTotal($order)
    {
        return $order->getGrandTotal();
    }

    /**
     * To get tax amount from order
     *
     * @param OrderInterface $order
     * @return float
     */
    protected function getTaxAmount($order)
    {
        return $order->getTaxAmount();
    }

    /**
     * To get discount amount from order
     *
     * @param OrderInterface $order
     * @return float
     */
    protected function getBaseDiscountAmount($order)
    {
        return $order->getBaseDiscountAmount();
    }

    /**
     * To get shipping amount from order
     *
     * @param OrderInterface $order
     * @return float
     */
    protected function getBaseShippingAmount($order)
    {
        return $order->getBaseShippingAmount();
    }
}
