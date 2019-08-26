<?php

namespace SomethingDigital\CheckoutShippingInfo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AddCustomFieldsToOrder implements ObserverInterface
{
    /**
     * Execute observer method.
     *
     * @param Observer $observer Observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $order->setData(
            'checkout_ponumber',
            $quote->getData('checkout_ponumber')
        );
        $order->setData(
            'checkout_shiptopo',
            $quote->getData('checkout_shiptopo')
        );
        $order->setData(
            'checkout_deliverypoint',
            $quote->getData('checkout_deliverypoint')
        );
        $order->setData(
            'checkout_ordernotes',
            $quote->getData('checkout_ordernotes')
        );
    }
}
