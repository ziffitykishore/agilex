<?php

namespace SomethingDigital\Order\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use SomethingDigital\Order\Model\OrderPlaceApi;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class OrderPlace implements ObserverInterface
{

    protected $logger;
    protected $orderPlaceApi;
    protected $arrayManager;
    protected $customerRepository;
    protected $orderRepository;
    protected $scopeConfig;

    /**
     * @param \DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        OrderPlaceApi $orderPlaceApi,
        ArrayManager $arrayManager,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->orderPlaceApi = $orderPlaceApi;
        $this->arrayManager = $arrayManager;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
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
        if ($response['status'] != 100 || !isset($response['body']['SxOrderId'])) {
            try {
                new \Zend\Mail('utf-8');
                $mail->setFrom(
                    $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE),
                    $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE)
                );
                $mail->addTo(
                    $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE),
                    $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE)
                );
                $mail->setSubject(__('Order %1 has not been sent to API', $order->getIncrementId());
                $mail->setBodyText(__('Order %1 has not been sent to API. Error Message: %2',$order->getIncrementId(), $response['body']));
                $mail->send();
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
            return;
        }

        $sxCustomerId = $this->arrayManager->get('body/SxCustomerId', $response);
        $sxContactId = $this->arrayManager->get('body/SxContactId', $response);
        $sxOrderId = $this->arrayManager->get('body/SxOrderId', $response);

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
        $order->setExtOrderId($sxOrderId);
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
}
