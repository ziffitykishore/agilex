<?php

namespace SomethingDigital\Order\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use SomethingDigital\Order\Model\OrderPlaceApi;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Customer\Api\CustomerRepositoryInterface;

class OrderPlace implements ObserverInterface
{

    protected $logger;
    protected $orderPlaceApi;
    protected $arrayManager;
    protected $customerRepository;

    /**
     * @param \DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        OrderPlaceApi $orderPlaceApi,
        ArrayManager $arrayManager,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->logger = $logger;
        $this->orderPlaceApi = $orderPlaceApi;
        $this->arrayManager = $arrayManager;
        $this->customerRepository = $customerRepository;
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
        $sxCustomerId = $this->arrayManager->get('body/SxCustomerId', $response);
        $sxContactId = $this->arrayManager->get('body/SxContactId', $response);

        if ($order->getCustomerId()) {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            if (!empty($sxCustomerId) && !$this->getTraversAccountId($customer)) {
                $customer->setCustomAttribute('travers_account_id', $sxCustomerId);
            }
            if (!empty($sxContactId) && !$this->getTraversContactId($customer)) {
                $customer->setCustomAttribute('travers_contact_id', $sxContactId);
            }
            $this->customerRepository->save($customer);
        }
    }

    protected function getTraversAccountId($customer)
    {
        if ($customer->getCustomAttribute('travers_account_id')) {
            return $customer->getCustomAttribute('travers_account_id')->getValue();
        }
        return '';
    }

    protected function getTraversContactId($customer)
    {
        if ($customer->getCustomAttribute('travers_contact_id')) {
            return $customer->getCustomAttribute('travers_contact_id')->getValue();
        }
        return '';
    }
}
