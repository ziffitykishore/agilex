<?php

namespace Travers\CustomerLinking\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Travers\CustomerLinking\Helper\Data;

class CustomerRegister implements ObserverInterface
{
    const RETRANS_PUBLISHER_TOPIC = 'customer_account_linking';

    public function __construct(
        PublisherInterface $publisher,
        Data $data
    ) {
        $this->publisher = $publisher;
        $this->helper = $data;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customer = $observer->getEvent()->getCustomer()->getId();
            $this->publisher->publish(self::RETRANS_PUBLISHER_TOPIC, $customer);
        }
        catch(\Exception $e) {
            $this->helper->logData($e->getMessage());
        }
    }
}