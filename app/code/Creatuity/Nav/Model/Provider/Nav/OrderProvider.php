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
    /**
     * @var Service
     */
    protected $orderService;

    /**
     * @var OrderDataManager
     */
    protected $findOrderOrderDataManager;

    /**
     * @var OrderDataManager
     */
    protected $createOrderDataManager;

    /**
     * @var OrderDataManager
     */
    protected $updateOrderWithMinimalInfo;

    /**
     * @var EntityParametersFactory
     */
    protected $entityParametersFactory;

    /**
     * @var EntityParametersFactory
     */
    protected $singleFieldParametersFactory;

    /**
     * @var EntityConflictResolverInterface
     */
    protected $entityConflictResolver;

    /**
     * @var array
     */
    protected $orderFieldDataExtractorMappings;

    /**
     * @var array
     */
    protected $customerFieldDataExtractorMappings;

    /**
     *
     * @param Service $orderService
     * @param OrderDataManager $findOrderOrderDataManager
     * @param OrderDataManager $createOrderDataManager
     * @param OrderDataManager $updateOrderWithMinimalInfo
     * @param EntityParametersFactory $entityParametersFactory
     * @param EntityConflictResolverInterface $entityConflictResolver
     * @param array $orderFieldDataExtractorMappings
     * @param array $customerFieldDataExtractorMappings
     */
    public function __construct(
        Service $orderService,
        OrderDataManager $findOrderOrderDataManager,
        OrderDataManager $createOrderDataManager,
        OrderDataManager $updateOrderWithMinimalInfo,
        EntityParametersFactory $entityParametersFactory,
        EntityConflictResolverInterface $entityConflictResolver,
        array $orderFieldDataExtractorMappings,
        array $customerFieldDataExtractorMappings
    ) {
        $this->orderService = $orderService;
        $this->findOrderOrderDataManager = $findOrderOrderDataManager;
        $this->createOrderDataManager = $createOrderDataManager;
        $this->updateOrderWithMinimalInfo = $updateOrderWithMinimalInfo;
        $this->entityParametersFactory = $entityParametersFactory;
        $this->entityConflictResolver = $entityConflictResolver;
        $this->orderFieldDataExtractorMappings = $orderFieldDataExtractorMappings;
        $this->customerFieldDataExtractorMappings = $customerFieldDataExtractorMappings;
    }

    /**
     * To Read, Create, Update Order from NAV.
     *
     * @param OrderInterface $order
     * @param array $customerData
     * @param string $updateOrderFlag
     * @return array
     */
    public function get(OrderInterface $order, array $customerData, $updateOrderFlag)
    {
        $orders = $this->getExistingOrders($order);

        $orderData = (empty($orders)) ? $this->createOrder($order) : $this->entityConflictResolver->resolve($orders);

        if ($updateOrderFlag == 'release_order') {
            return $orderData;
        }
        if ($updateOrderFlag == 'order_update_minimal_info') {
            return $this->updateOrderWithMinimalInfo($order,$orderData);
        }

        if ($updateOrderFlag == 'order_update_full_info') {
            return $this->updateOrder($order, $orderData, $customerData);
        }
    }

    /**
     * To Read existing order from NAV
     *
     * @param OrderInterface $order
     * @return array
     */
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

    /**
     * To Create new order in NAV
     *
     * @param OrderInterface $order
     * @return array
     */
    protected function createOrder(OrderInterface $order)
    {
        $newOrder = $this->orderService->process(
            new ServiceRequest(
                new CreateOperation(),
                new SingleDimension(),
                $this->entityParametersFactory->create()
            )
        );

        return $newOrder;
    }

    /**
     * To Update order with minimal information
     *
     * @param OrderInterface $order
     * @param array $newOrder
     * @return array
     */
    protected function updateOrderWithMinimalInfo(OrderInterface $order, $newOrder)
    {

        $data['No'] = $newOrder['No'];
        $data['Key'] = $newOrder['Key'];
        $params = array_merge($data, $this->updateOrderWithMinimalInfo->process($order));

        $parameters = $this->entityParametersFactory->create($params);

        $orderData = $this->orderService->process(
            new ServiceRequest(
                new UpdateOperation(),
                new SingleDimension(),
                $parameters
            )
        );

        return $orderData;
    }

    /**
     * To Update order with full information
     *
     * @param OrderInterface $order
     * @param array $intermediateOrderData
     * @param array $intermediateCustomerData
     * @return array
     */
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
