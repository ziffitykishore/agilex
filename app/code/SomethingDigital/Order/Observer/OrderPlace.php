<?php

namespace SomethingDigital\Order\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use SomethingDigital\Order\Model\OrderPlaceApi;

class OrderPlace implements ObserverInterface
{

    protected $logger;
    protected $orderPlaceApi;

    /**
     * @param \DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        OrderPlaceApi $orderPlaceApi
    ) {
        $this->logger = $logger;
        $this->orderPlaceApi = $orderPlaceApi;
    }

    /**
     * Send order
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        try {
            $this->orderPlaceApi->sendOrder($order);
        } catch (\Exception $e) {
            $this->logger->alert($e);
        }
    }
}
