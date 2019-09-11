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

class OrderUpdateTask implements TaskInterface
{
    protected $dataObjectFactory;
    protected $logger;
    protected $orderCollectionMap;
    protected $orderCustomerProvider;
    protected $orderProvider;
    protected $orderLineProvider;
    protected $taxFinalOrderLineProvider;
    protected $discountFinalOrderLineProvider;
    protected $shippingFinalOrderLineProvider;
    protected $orderReleaseManager;
    protected $orderDeleteManager;
    protected $orderStatusDataProcessor;
    protected $lineNumberDataGenerator;
    protected $orderPrimaryKeyFieldDataExtractor;
    protected $orderItemFilters;

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
        array $orderItemFilters = []
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
    }

    public function execute()
    {
        foreach ($this->orderCollectionMap->getPageIndices() as $pageIndex) {
            $this->orderCollectionMap->setPage($pageIndex);

            foreach ($this->orderCollectionMap->getKeys() as $orderIncrementId) {
                $this->updateOrder($this->orderCollectionMap->get($orderIncrementId));
            }
        }
    }

    protected function updateOrder(OrderInterface $order)
    {
        $customerData = [];
        $error = false;

        try {
            $customerData = $this->orderCustomerProvider->get($order);

            $orderData = $this->orderProvider->get($order, $customerData);

            foreach ($order->getAllItems() as $orderItem) {
                if ($this->isOrderItemFiltered($orderItem)) {
                    continue;
                }

                $this->orderLineProvider->get($orderItem, $orderData, $this->lineNumberDataGenerator->generate());
            }

            $this->taxFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());
            $this->discountFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());
            $this->shippingFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());

            $this->orderReleaseManager->process($this->orderProvider->get($order, $customerData));

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
