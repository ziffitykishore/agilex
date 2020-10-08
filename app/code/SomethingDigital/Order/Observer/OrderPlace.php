<?php

namespace SomethingDigital\Order\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use SomethingDigital\Order\Model\OrderPlaceApi;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SomethingDigital\Order\Helper\Email;
use Magento\Framework\Exception\NoSuchEntityException;

class OrderPlace implements ObserverInterface
{

    protected $logger;
    protected $orderPlaceApi;
    protected $arrayManager;
    protected $customerRepository;
    protected $addressRepository;
    protected $orderRepository;
    protected $scopeConfig;
    protected $email;

    /**
     * @param \DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        OrderPlaceApi $orderPlaceApi,
        ArrayManager $arrayManager,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig,
        Email $email
    ) {
        $this->logger = $logger;
        $this->orderPlaceApi = $orderPlaceApi;
        $this->arrayManager = $arrayManager;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->email = $email;
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
            $response = $this->orderPlaceApi->sendOrder($order);

            $this->processResponse($order, $response);
        } catch (\Exception $e) {
            $this->logger->alert($e);
        }
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
        if (!$this->orderPlaceApi->isSuccessful($response['status']) || !isset($response['body']['SxOrderId'])) {
            try {
                $order->setSxIntegrationStatus('failed');
                $order->setSxIntegrationResponse($response['body']);
                $this->orderRepository->save($order);
            } catch (\Exception $e) {

            }
            $this->email->sendEmail($order, $response);
            return;
        }

        $sxCustomerId = $this->arrayManager->get('body/SxCustomerId', $response);
        $sxContactId = $this->arrayManager->get('body/SxContactId', $response);
        $sxOrderId = $this->arrayManager->get('body/SxOrderId', $response);
        $shipToId = $this->arrayManager->get('body/ShipToId', $response);

        if ($order->getCustomerId()) {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            if (!empty($sxCustomerId) && !$this->getTraversAccountId($customer)) {
                $customer->setCustomAttribute('travers_account_id', $sxCustomerId);
            }
            if (!empty($sxContactId) && !$this->getTraversContactId($customer)) {
                $customer->setCustomAttribute('travers_contact_id', $sxContactId);
            }
            $this->customerRepository->save($customer);
            try {
                $shippingAddress = $order->getShippingAddress();
                $customerAddressId = $shippingAddress->getCustomerAddressId();
                $address = $this->addressRepository->getById($customerAddressId);
                if (!empty($shipToId) && !$this->getTraversShipToId($address)) {
                    $address->setCustomAttribute('sx_address_id', $shipToId);
                }
                $this->addressRepository->save($address);
            } catch (NoSuchEntityException $e) {
                //no action needed
            }
        }
        $order->setRealOrderId($sxOrderId);
        $order->setSxIntegrationStatus('processing');
        $order->setSxIntegrationResponse(json_encode($response['body']));
        $this->orderRepository->save($order);
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

    protected function getTraversShipToId($address)
    {
        if ($address->getCustomAttribute('sx_address_id')) {
            return $address->getCustomAttribute('sx_address_id')->getValue();
        }
        return '';
    }
}
