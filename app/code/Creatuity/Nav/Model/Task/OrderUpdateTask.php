<?php

namespace Creatuity\Nav\Model\Task;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Creatuity\Nav\Model\Data\Processor\OrderStatusDataProcessor;
use Creatuity\Nav\Model\Data\Extractor\Nav\FieldDataExtractor;
use Creatuity\Nav\Model\Map\CollectionMap;
use Creatuity\Nav\Model\Provider\Nav\CustomerProvider;
use Creatuity\Nav\Model\Provider\Nav\FinalOrderLineProvider;
use Creatuity\Nav\Model\Provider\Nav\OrderLineProvider;
use Creatuity\Nav\Model\Provider\Nav\OrderProvider;
use Creatuity\Nav\Model\Task\Data\Generator\LineNumberDataGenerator;
use Creatuity\Nav\Model\Task\Manager\NavEntityOperationManager;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderUpdateTask implements TaskInterface
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionMap
     */
    protected $orderCollectionMap;

    /**
     * @var CustomerProvider
     */
    protected $orderCustomerProvider;

    /**
     * @var OrderProvider
     */
    protected $orderProvider;

    /**
     * @var OrderLineProvider
     */
    protected $orderLineProvider;

    /**
     * @var FinalOrderLineProvider
     */
    protected $taxFinalOrderLineProvider;

    /**
     * @var FinalOrderLineProvider
     */
    protected $discountFinalOrderLineProvider;

    /**
     * @var FinalOrderLineProvider
     */
    protected $shippingFinalOrderLineProvider;

    /**
     * @var NavEntityOperationManager
     */
    protected $orderReleaseManager;

    /**
     * @var NavEntityOperationManager
     */
    protected $orderDeleteManager;

    /**
     * @var OrderStatusDataProcessor
     */
    protected $orderStatusDataProcessor;

    /**
     * @var LineNumberDataGenerator
     */
    protected $lineNumberDataGenerator;

    /**
     * @var FieldDataExtractor
     */
    protected $orderPrimaryKeyFieldDataExtractor;

    /**
     * @var array
     */
    protected $orderItemFilters;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     *
     * @param DataObjectFactory $dataObjectFactory
     * @param LoggerInterface $logger
     * @param CollectionMap $orderCollectionMap
     * @param CustomerProvider $orderCustomerProvider
     * @param OrderProvider $orderProvider
     * @param OrderLineProvider $orderLineProvider
     * @param FinalOrderLineProvider $taxFinalOrderLineProvider
     * @param FinalOrderLineProvider $discountFinalOrderLineProvider
     * @param FinalOrderLineProvider $shippingFinalOrderLineProvider
     * @param NavEntityOperationManager $orderReleaseManager
     * @param NavEntityOperationManager $orderDeleteManager
     * @param OrderStatusDataProcessor $orderStatusDataProcessor
     * @param LineNumberDataGenerator $lineNumberDataGenerator
     * @param FieldDataExtractor $orderPrimaryKeyFieldDataExtractor
     * @param array $orderItemFilters
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $logger,
        CollectionMap $orderCollectionMap,
        CustomerProvider $orderCustomerProvider,
        OrderProvider $orderProvider,
        OrderLineProvider $orderLineProvider,
        FinalOrderLineProvider $taxFinalOrderLineProvider,
        FinalOrderLineProvider $discountFinalOrderLineProvider,
        FinalOrderLineProvider $shippingFinalOrderLineProvider,
        NavEntityOperationManager $orderReleaseManager,
        NavEntityOperationManager $orderDeleteManager,
        OrderStatusDataProcessor $orderStatusDataProcessor,
        LineNumberDataGenerator $lineNumberDataGenerator,
        FieldDataExtractor $orderPrimaryKeyFieldDataExtractor,
        array $orderItemFilters = [],
        OrderRepositoryInterface $orderRepository
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
        $this->orderCollectionMap = $orderCollectionMap;
        $this->orderCustomerProvider = $orderCustomerProvider;
        $this->orderProvider = $orderProvider;
        $this->orderLineProvider = $orderLineProvider;
        $this->taxFinalOrderLineProvider = $taxFinalOrderLineProvider;
        $this->discountFinalOrderLineProvider = $discountFinalOrderLineProvider;
        $this->shippingFinalOrderLineProvider = $shippingFinalOrderLineProvider;
        $this->orderReleaseManager = $orderReleaseManager;
        $this->orderDeleteManager = $orderDeleteManager;
        $this->orderStatusDataProcessor = $orderStatusDataProcessor;
        $this->lineNumberDataGenerator = $lineNumberDataGenerator;
        $this->orderPrimaryKeyFieldDataExtractor = $orderPrimaryKeyFieldDataExtractor;
        $this->orderItemFilters = $orderItemFilters;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute method
     *
     * @return void
     */
    public function execute()
    {
        foreach ($this->orderCollectionMap->getPageIndices() as $pageIndex) {
            $this->orderCollectionMap->setPage($pageIndex);
            foreach ($this->orderCollectionMap->getKeys() as $orderIncrementId) {
                $this->updateOrder($this->orderCollectionMap->get($orderIncrementId));
            }
        }
    }

    /**
     * To Update order to NAV.
     *
     * @param OrderInterface $order
     */
    protected function updateOrder(OrderInterface $order)
    {
        $customerData = [];
        $error = false;

        try {
            $customerData = $this->orderCustomerProvider->get($order);
            $orderData = $this->orderProvider->get($order, $customerData, 'order_update_minimal_info');
            foreach ($order->getAllItems() as $orderItem) {
                if ($this->isOrderItemFiltered($orderItem)) {
                    continue;
                }

                $this->orderLineProvider->get($orderItem, $orderData, $this->lineNumberDataGenerator->generate());
            }
            $orderData = $this->orderProvider->get($order, $customerData, 'order_update_full_info');

            /* Commented due to NO USE
            $this->taxFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());
            $this->discountFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());
             Commented due to NO USE */
            $this->shippingFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());

            $orderData = $this->orderProvider->get($order, $customerData, 'release_order');
            $this->orderReleaseManager->process($orderData);

            #To store Nav Order Id to Magento.
            if ($orderData !== null && $orderData['No']) {
                $currentOrder = $this->orderRepository->get($order->getEntityId());
                $currentOrder->setNavOrderId($orderData['No']);
                $currentOrder->save();
            }

            $this->orderStatusDataProcessor->process(
                $order,
                $this->dataObjectFactory->create([
                    'increment_id' => $this->orderPrimaryKeyFieldDataExtractor->extract($orderData),
                ])
            );
        } catch (Exception $e) {
            $this->logger->critical($e);

            $error = true;
        }

        if ($error) {
            try {
                $this->orderDeleteManager->process($this->orderProvider->get($order, $customerData));
            } catch (Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    /**
     * To filter OrderItem
     *
     * @param OrderItemInterface $orderItem
     * @return boolean
     */
    protected function isOrderItemFiltered(OrderItemInterface $orderItem)
    {
        foreach ($this->orderItemFilters as $filter) {
            if ($filter->isFiltered($orderItem)) {
                return true;
            }
        }

        return false;
    }
}
