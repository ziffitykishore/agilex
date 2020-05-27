<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vantiv\Payment\Model\Recurring\Subscription;

class ToOrderPayment
{
    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    private $objectCopyService;

    /**
     * @var \Magento\Sales\Api\OrderPaymentRepositoryInterface
     */
    private $orderPaymentRepository;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @param \Magento\Sales\Api\OrderPaymentRepositoryInterface $orderPaymentRepository
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        \Magento\Sales\Api\OrderPaymentRepositoryInterface $orderPaymentRepository,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->objectCopyService = $objectCopyService;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->eventManager = $eventManager;
    }

    /**
     * Create order payment based on subscription data
     *
     * @param \Vantiv\Payment\Model\Recurring\Subscription $subscription
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $data
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    public function convert(
        \Vantiv\Payment\Model\Recurring\Subscription $subscription,
        \Magento\Sales\Api\Data\OrderInterface $order,
        $data = []
    ) {
        $paymentData = $this->objectCopyService->getDataFromFieldset(
            'vantiv_subscription_convert',
            'to_order_payment',
            $subscription
        );

        $orderPayment = $this->orderPaymentRepository->create();

        $this->dataObjectHelper->populateWithArray(
            $orderPayment,
            array_merge($paymentData, $data),
            '\Magento\Sales\Api\Data\OrderPaymentInterface'
        );

        $this->eventManager->dispatch(
            'vantiv_subscription_convert_to_order_payment',
            ['payment' => $orderPayment, 'subscription' => $subscription]
        );

        if (!$orderPayment->hasAmountOrdered()) {
            $orderPayment->setAmountOrdered($order->getTotalDue());
        }

        return $orderPayment;
    }
}
