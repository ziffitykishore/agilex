<?php

namespace Creatuity\Nav\Model\Data\Processor;

use Magento\Framework\DataObject;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class OrderStatusDataProcessor implements DataProcessorInterface
{
    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param string $state
     * @param status $status
     * @param OrderRepositoryInterface $orderRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        $state,
        $status,
        OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->state = $state;
        $this->status = $status;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function process(DataObject $orderData, DataObject $intermediateData)
    {
        $navOrderId = $intermediateData->getIncrementId();
        $statusFormatted = ucfirst(
            $this->scopeConfig->getValue(
                $this->status,
                ScopeInterface::SCOPE_STORE
            )
        );
        $comment = "Set Magento Order Status to <b>{$statusFormatted}</b> for NAV Order <b>{$navOrderId}</b>";

        $order = $this->orderRepository->get($orderData->getId())
            ->setState(
                $this->scopeConfig->getValue(
                    $this->state,
                    ScopeInterface::SCOPE_STORE
                )
            )
            ->addStatusToHistory(
                $this->scopeConfig->getValue(
                    $this->status,
                    ScopeInterface::SCOPE_STORE
                ),
                $comment
            );

        $this->orderRepository->save($order);
    }
}
