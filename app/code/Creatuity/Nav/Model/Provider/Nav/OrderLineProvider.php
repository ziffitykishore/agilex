<?php

namespace Creatuity\Nav\Model\Provider\Nav;

use Magento\Sales\Api\Data\OrderItemInterface;
use Creatuity\Nav\Model\Service\Service;
use Creatuity\Nav\Model\Service\Request\Dimension\MultipleDimension;
use Creatuity\Nav\Model\Service\Request\Dimension\SingleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\CreateOperation;
use Creatuity\Nav\Model\Service\Request\Operation\ReadOperation;
use Creatuity\Nav\Model\Service\Request\Operation\UpdateOperation;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\FilterGroup;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\SingleValueFilter;
use Creatuity\Nav\Model\Service\Request\Parameters\FilterParameters;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Service\Request\Parameters\EntityParametersFactory;
use Creatuity\Nav\Model\Task\ConflictResolver\EntityConflictResolverInterface;
use Creatuity\Nav\Model\Data\Manager\Magento\OrderItemDataManager;

class OrderLineProvider
{
    protected $orderLineService;
    protected $entityParametersFactory;
    protected $entityConflictResolver;
    protected $findOrderLineOrderItemDataManager;
    protected $updateOrderLineOrderItemDataManager;
    protected $findOrderFieldDataExtractorMappings;
    protected $createOrderFieldDataExtractorMappings;
    protected $updateOrderFieldDataExtractorMappings;

    public function __construct(
        Service $orderLineService,
        EntityParametersFactory $entityParametersFactory,
        EntityConflictResolverInterface $entityConflictResolver,
        OrderItemDataManager $findOrderLineOrderItemDataManager,
        OrderItemDataManager $updateOrderLineOrderItemDataManager,
        array $findOrderFieldDataExtractorMappings,
        array $createOrderFieldDataExtractorMappings,
        array $updateOrderFieldDataExtractorMappings
    ) {
        $this->orderLineService = $orderLineService;
        $this->entityParametersFactory = $entityParametersFactory;
        $this->entityConflictResolver = $entityConflictResolver;
        $this->findOrderLineOrderItemDataManager = $findOrderLineOrderItemDataManager;
        $this->updateOrderLineOrderItemDataManager = $updateOrderLineOrderItemDataManager;
        $this->findOrderFieldDataExtractorMappings = $findOrderFieldDataExtractorMappings;
        $this->createOrderFieldDataExtractorMappings = $createOrderFieldDataExtractorMappings;
        $this->updateOrderFieldDataExtractorMappings = $updateOrderFieldDataExtractorMappings;
    }

    public function get(OrderItemInterface $orderItem, array $orderData, array $orderLineDataExternal)
    {
        $orderLines = $this->getExistingOrderLines($orderItem, $orderData, $orderLineDataExternal);

        $orderLineData = (empty($orderLines))
            ? $this->createOrderLine($orderData, $orderLineDataExternal)
            : $this->entityConflictResolver->resolve($orderLines)
        ;

        return $this->updateOrderLine($orderItem, $orderLineData, $orderLineDataExternal);
    }

    protected function getExistingOrderLines(OrderItemInterface $orderItem, array $orderData, array $orderLineData)
    {
        $data = [];
        foreach ($this->findOrderFieldDataExtractorMappings as $dataExtractorMapping) {
            $data = array_merge($data, $dataExtractorMapping->apply($orderData));
        }

        $data = array_merge($data, $this->findOrderLineOrderItemDataManager->process($orderItem));

        $data = array_merge($data, $orderLineData);

        $filters = [];
        foreach ($data as $field => $value) {
            $filters[] = new SingleValueFilter($field, $value);
        }

        return $this->orderLineService->process(
            new ServiceRequest(
                new ReadOperation(),
                new MultipleDimension(),
                new FilterParameters(
                    new FilterGroup($filters)
                )
            )
        );
    }

    protected function createOrderLine(array $orderData, array $orderLineDataExternal)
    {
        $data = [];
        foreach ($this->createOrderFieldDataExtractorMappings as $dataExtractorMapping) {
            $data = array_merge($data, $dataExtractorMapping->apply($orderData));
        }

        $data = array_merge($data, $orderLineDataExternal);

        $orderLine = $this->orderLineService->process(
            new ServiceRequest(
                new CreateOperation(),
                new SingleDimension(),
                $this->entityParametersFactory->create($data)
            )
        );

        return $orderLine;
    }

    protected function updateOrderLine(OrderItemInterface $orderItem, array $orderLineData, array $orderLineDataExternal)
    {
        $data = [];
        foreach ($this->updateOrderFieldDataExtractorMappings as $dataExtractorMapping) {
            $data = array_merge($data, $dataExtractorMapping->apply($orderLineData));
        }

        $data = array_merge($data, $orderLineDataExternal);

        $data = array_merge($data, $this->updateOrderLineOrderItemDataManager->process($orderItem));

        $orderLine = $this->orderLineService->process(
            new ServiceRequest(
                new UpdateOperation(),
                new SingleDimension(),
                $this->entityParametersFactory->create($data)
            )
        );

        return $orderLine;
    }
}
