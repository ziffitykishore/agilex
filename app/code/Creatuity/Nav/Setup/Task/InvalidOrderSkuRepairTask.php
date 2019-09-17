<?php

namespace Creatuity\Nav\Setup\Task;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Creatuity\Nav\Model\Task\TaskInterface;

class InvalidOrderSkuRepairTask implements TaskInterface
{
    protected $searchCriteriaBuilder;
    protected $orderRepository;
    protected $orderItemRepository;
    protected $orderIncrementIds;
    protected $skuFind;
    protected $skuReplace;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        array $orderIncrementIds,
        $skuFind,
        $skuReplace
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderIncrementIds = $orderIncrementIds;
        $this->skuFind = $skuFind;
        $this->skuReplace = $skuReplace;
    }

    public function execute()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderInterface::INCREMENT_ID, $this->orderIncrementIds, 'in')
            ->create()
        ;

        $orders = $this->orderRepository
            ->getList($searchCriteria)
            ->getItems()
        ;

        foreach ($orders as $order) {
            foreach ($order->getItems() as $orderItem) {
                if ($orderItem->getSku() === $this->skuFind) {
                    $orderItem->setSku($this->skuReplace);
                    $this->orderItemRepository->save($orderItem);
                }
            }
        }
    }
}
