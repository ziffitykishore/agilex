<?php

namespace Travers\CustomerLinking\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Travers\CustomerLinking\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CustomerRegister implements ObserverInterface
{
    const RETRANS_PUBLISHER_TOPIC = 'customer_account_linking';

    public function __construct(
        PublisherInterface $publisher,
        Data $data,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->publisher = $publisher;
        $this->helper = $data;
        $this->customerRepository = $customerRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customer = $observer->getEvent()->getCustomer();
            $customer = $this->customerRepository->getById($customer->getId());
            if($customer->getCustomAttribute('travers_account_id') != null)
                $this->publisher->publish(self::RETRANS_PUBLISHER_TOPIC, $customer->getId());
        }
        catch(\Exception $e) {
            $this->helper->logData($e->getMessage());
        }
    }
}