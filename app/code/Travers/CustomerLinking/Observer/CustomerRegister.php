<?php

namespace Travers\CustomerLinking\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;

class CustomerRegister implements ObserverInterface
{
    const RETRANS_PUBLISHER_TOPIC = 'customer_account_linking';

    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer()->getId();
        $this->publisher->publish(self::RETRANS_PUBLISHER_TOPIC, $customer);
    }
}