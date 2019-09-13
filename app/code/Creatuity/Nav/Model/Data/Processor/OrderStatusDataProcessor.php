<?php

namespace Creatuity\Nav\Model\Data\Processor;

use Magento\Framework\DataObject;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderStatusDataProcessor implements DataProcessorInterface
{
    protected $state;
    protected $status;
    protected $orderRepository;

    public function __construct(
        $state,
        $status,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->state = $state;
        $this->status = $status;
        $this->orderRepository = $orderRepository;
    }

    public function process(DataObject $orderData, DataObject $intermediateData)
    {
        $navOrderId = $intermediateData->getIncrementId();
        $statusFormatted = ucfirst($this->status);
        $comment = "Set Magento Order Status to <b>{$statusFormatted}</b> for NAV Order <b>{$navOrderId}</b>";

        $order = $this->orderRepository->get($orderData->getId())
            ->setState($this->state)
            ->addStatusToHistory($this->status, $comment)
        ;

        $this->orderRepository->save($order);
    }
}
