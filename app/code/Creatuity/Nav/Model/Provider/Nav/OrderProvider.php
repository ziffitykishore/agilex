<?php

namespace Creatuity\Nav\Model\Provider\Nav;

use Magento\Sales\Api\Data\OrderInterface;
use Creatuity\Nav\Model\Service\Service;
use Creatuity\Nav\Model\Service\Request\Dimension\SingleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\CreateOperation;
use Creatuity\Nav\Model\Service\Request\Operation\UpdateOperation;
use Creatuity\Nav\Model\Service\Request\Parameters\EntityParametersFactory;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\SingleValueFilter;
use Creatuity\Nav\Model\Service\Request\Dimension\MultipleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\ReadOperation;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\FilterGroup;
use Creatuity\Nav\Model\Service\Request\Parameters\FilterParameters;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Task\ConflictResolver\EntityConflictResolverInterface;
use Creatuity\Nav\Model\Data\Manager\Magento\OrderDataManager;

class OrderProvider
{
    protected $orderService;
    protected $findOrderOrderDataManager;
    protected $createOrderDataManager;
    protected $entityParametersFactory;
    protected $singleFieldParametersFactory;
    protected $entityConflictResolver;
    protected $orderFieldDataExtractorMappings;
    protected $customerFieldDataExtractorMappings;

    public function __construct(
        Service $orderService,
        OrderDataManager $findOrderOrderDataManager,
        OrderDataManager $createOrderDataManager,
        EntityParametersFactory $entityParametersFactory,
        EntityConflictResolverInterface $entityConflictResolver,
        array $orderFieldDataExtractorMappings,
        array $customerFieldDataExtractorMappings
    ) {
        $this->orderService = $orderService;
        $this->findOrderOrderDataManager = $findOrderOrderDataManager;
        $this->createOrderDataManager = $createOrderDataManager;
        $this->entityParametersFactory = $entityParametersFactory;
        $this->entityConflictResolver = $entityConflictResolver;
        $this->orderFieldDataExtractorMappings = $orderFieldDataExtractorMappings;
        $this->customerFieldDataExtractorMappings = $customerFieldDataExtractorMappings;
    }

    public function get(OrderInterface $order, array $customerData)
    {
        $orders = $this->getExistingOrders($order);

        $orderData = (empty($orders)) ? $this->createOrder() : $this->entityConflictResolver->resolve($orders);

        return $this->updateOrder($order, $orderData, $customerData);
    }

    protected function getExistingOrders(OrderInterface $order)
    {
        $findOrderQueryData = $this->findOrderOrderDataManager->process($order);

        $filters = [];
        foreach ($findOrderQueryData as $field => $value) {
            $filters[] = new SingleValueFilter($field, $value);
        }

        return $this->orderService->process(
            new ServiceRequest(
                new ReadOperation(),
                new MultipleDimension(),
                new FilterParameters(
                    new FilterGroup($filters)
                )
            )
        );
    }

    protected function createOrder()
    {
        $order = $this->orderService->process(
            new ServiceRequest(
                new CreateOperation(),
                new SingleDimension(),
                $this->entityParametersFactory->create()
            )
        );

        return $order;
    }

    protected function updateOrder(OrderInterface $order, array $intermediateOrderData, array $intermediateCustomerData)
    {
        $data = [];
        foreach ($this->orderFieldDataExtractorMappings as $dataExtractorMapping) {
            $data = array_merge($data, $dataExtractorMapping->apply($intermediateOrderData));
        }
        foreach ($this->customerFieldDataExtractorMappings as $dataExtractorMapping) {
            $data = array_merge($data, $dataExtractorMapping->apply($intermediateCustomerData));
        }
        $data = array_merge($data, $this->createOrderDataManager->process($order));

        $parameters = $this->entityParametersFactory->create($data);

        $orderData = $this->orderService->process(
            new ServiceRequest(
                new UpdateOperation(),
                new SingleDimension(),
                $parameters
            )
        );

        return $orderData;
    }
}
