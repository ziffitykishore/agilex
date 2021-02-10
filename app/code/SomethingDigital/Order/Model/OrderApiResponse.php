<?php

namespace SomethingDigital\Order\Model;

use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use SomethingDigital\Order\Helper\Email;
use Magento\Framework\Exception\NoSuchEntityException;

class OrderApiResponse
{

    protected $logger;
    protected $arrayManager;
    protected $customerRepository;
    protected $addressRepository;
    protected $orderRepository;
    protected $email;

    /**
     * @param \DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        ArrayManager $arrayManager,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        OrderRepositoryInterface $orderRepository,
        Email $email
    ) {
        $this->logger = $logger;
        $this->arrayManager = $arrayManager;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
        $this->email = $email;
    }

    /**
     * Process API response
     *
     *
     * @param OrderInterface $order
     * @param array $response
     */
    public function process($order, $response, $status)
    {
        if (!$status || !isset($response['body']['SxOrderId'])) {
            $this->logger->alert('Response from middleware order endpoint with error:' . json_encode($response) . ' Status: ' . $status);
            try {
                if($response['status'] == 400) {
                    $order->setState('holded')->setStatus('holded');
                    if($order->getSxRetryCount() == null)
                        $order->setSxRetryCount(4);
                }
                else {
                    $order->setState('Rejected')->setStatus('Rejected');
                }
                $order->setSxIntegrationStatus('failed');
                $order->setSxIntegrationResponse(json_encode($response['body']));
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $this->logger->alert('Could not save an order with entity id: ' . $order->getEntityId() .
                    ' and set sx order status when processing response from middleware API order.' . $e->getMessage());
            }
            $this->email->sendEmail($order, $response);
            return [
                'status' => false
            ];
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
                $this->logger->alert('Could not save address and set sx_address_id when processing response from middleware API order.' .
                    ' Order Entity Id:' . $order->getEntityId() . ' ' . $e->getMessage());
                $this->logger->alert('Response from middleware order endpoint:' . json_encode($response));
                return [
                    'status' => false,
                    'error' => __($e->getMessage())
                ];
            }
        }
        $order->setRealOrderId($sxOrderId);
        $order->setState('processing')->setStatus('processing');
        $order->setSxIntegrationStatus('processing');
        $order->setSxIntegrationResponse(json_encode($response['body']));
        $this->orderRepository->save($order);

        return [
            'status' => true
        ];
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
