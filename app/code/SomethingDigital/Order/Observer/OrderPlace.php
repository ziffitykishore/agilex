<?php

namespace SomethingDigital\Order\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use SomethingDigital\Order\Model\OrderPlaceApi;

class OrderPlace implements ObserverInterface
{

    protected $logger;
    protected $orderPlaceApi;

    /**
     * @param DateTime $dateTime
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
        
        $response = $this->orderPlaceApi->sendOrder($order);

        $this->processResponse($order, $response);
    }

    /**
     * Process API response
     *
     *
     * @param OrderInterface $order
     * @param array $response
     */
    protected function processResponse($order, $response)
    {
        //TO DO
    }
}
