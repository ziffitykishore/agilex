<?php

namespace PartySupplies\Order\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Order implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     *
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * To save nav_customer_id to order
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $customer = $this->customerRepository->getById($order->getCustomerId());

        if ($customer->getCustomAttribute('nav_customer_id') !== null) {
            $order->setNavCustomerId($customer->getCustomAttribute('nav_customer_id')->getValue());
            $order->save();
        }
    }
}
