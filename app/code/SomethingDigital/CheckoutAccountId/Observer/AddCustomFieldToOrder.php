<?php

namespace SomethingDigital\CheckoutAccountId\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AddCustomFieldToOrder implements ObserverInterface
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
            'travers_account_id',
            $quote->getData('travers_account_id')
        );
    }
}
