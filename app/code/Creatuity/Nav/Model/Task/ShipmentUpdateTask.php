<?php

namespace Creatuity\Nav\Model\Task;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Creatuity\Nav\Model\Data\Processor\Manager\DataProcessorManager;
use Creatuity\Nav\Model\Map\CollectionMap;
use Creatuity\Nav\Model\Data\Manager\Nav\DataManager;
use Creatuity\Nav\Model\Map\Provider\StaticCollectionProvider;
use Creatuity\Nav\Model\Service\Request\Dimension\MultipleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\ReadOperation;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\FilterGroup;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\SingleValueFilter;
use Creatuity\Nav\Model\Service\Request\Parameters\FilterParameters;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Service\Service;

class ShipmentUpdateTask implements TaskInterface
{
    protected $logger;
    protected $navFilterField;
    protected $orderCollectionMap;
    protected $orderItemCollectionMap;
    protected $postedPackageService;
    protected $navShipmentTrackDataManager;
    protected $navShipmentItemDataManager;
    protected $navShipmentPackageDataManager;
    protected $dataObjectFactory;
    protected $dataProcessorManager;

    public function __construct(
        LoggerInterface $logger,
        $navFilterField,
        CollectionMap $orderCollectionMap,
        Service $postedPackageService,
        DataManager $navShipmentTrackDataManager,
        DataManager $navShipmentItemDataManager,
        DataManager $navShipmentPackageDataManager,
        DataObjectFactory $dataObjectFactory,
        DataProcessorManager $dataProcessorManager
    ) {
        $this->logger = $logger;
        $this->navFilterField = $navFilterField;
        $this->orderCollectionMap = $orderCollectionMap;
        $this->postedPackageService = $postedPackageService;
        $this->navShipmentTrackDataManager = $navShipmentTrackDataManager;
        $this->navShipmentItemDataManager = $navShipmentItemDataManager;
        $this->navShipmentPackageDataManager = $navShipmentPackageDataManager;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataProcessorManager = $dataProcessorManager;
    }

    public function execute()
    {
        foreach ($this->orderCollectionMap->getPageIndices() as $pageIndex) {
            $this->orderCollectionMap->setPage($pageIndex);

            foreach ($this->orderCollectionMap->getKeys() as $orderIncrementId) {
                $this->updateShipments(
                    $this->orderCollectionMap->get($orderIncrementId),
                    $orderIncrementId
                );
            }
        }
    }

    protected function updateShipments(OrderInterface $order, $orderIncrementId)
    {
        try {
            if (!$order->canShip()) {
                return;
            }

            $existingShipmentsMap = $this->getExistingShipmentsMap($order);

            $orderItemCollectionMap = $this->getOrderItemCollectionMap($order);

            $navShipments = $this->fetchNavShipments($orderIncrementId);
            foreach ($navShipments as $navShipment) {
                $this->createShipment($orderItemCollectionMap, $order, $navShipment, $existingShipmentsMap);
            }
        } catch (Exception $e) {
            $this->logger->critical($e);
        }
    }

    protected function createShipment(CollectionMap $orderItemCollectionMap, OrderInterface $order, array $navShipmentData, array $existingShipmentsMap)
    {
        // TODO: strip empty members from the array using API-driven system
        if (!isset($navShipmentData['Posted_Package_Line'])) {
            return;
        }

        // TODO: Replace logic below with API-driven system(s) to access the Posted_Package_Line items
        $navShipmentItems = $navShipmentData['Posted_Package_Line']->Posted_Package_Line;
        if (!is_array($navShipmentItems)) {
            $navShipmentItems = [$navShipmentItems];
        }

        $navShipmentItemMap = [];
        foreach ($navShipmentItems as $navShipmentItem) {
            // TODO: normalize single members in the array using API-driven system
            $navShipmentItem = $this->navShipmentItemDataManager->process((array)$navShipmentItem);
            if (!isset($navShipmentItem[ProductInterface::SKU])) {
                throw new Exception("Sku field not defined in NAV shipment item data");
            }
            $sku = $navShipmentItem[ProductInterface::SKU];

            if (!isset($navShipmentItem[ShipmentItemInterface::QTY])) {
                throw new Exception("Qty field not defined in NAV shipment item data");
            }
            $qty = $navShipmentItem[ShipmentItemInterface::QTY];

            $navShipmentItemMap[$sku] = $qty;
        }


        if (in_array($navShipmentItemMap, $existingShipmentsMap)) {
            return;
        }


        $shipmentItems = [];
        foreach ($navShipmentItemMap as $sku => $qty) {
            $orderItems = [];
            foreach ($orderItemCollectionMap->getPageIndices() as $pageIndex) {
                $orderItemCollectionMap->setPage($pageIndex);

                foreach ($orderItemCollectionMap->getKeys() as $orderItemId) {
                    $orderItem = $orderItemCollectionMap->get($orderItemId);
                    if ($orderItem->getSku() == $sku && $orderItem->getParentItemId() === null) {
                        $orderItems[] = $orderItem;
                    }
                }
            }

            foreach ($orderItems as $orderItem) {
                $qtyToShip = $orderItem->getQtyToShip();
                $shippableQty = ($qty >= $qtyToShip) ? $qtyToShip : $qty;
                $shipmentItems[$orderItem->getId()] = $shippableQty;
                $qty -= $shippableQty;
            }
        }


        $navTrackData = $this->navShipmentTrackDataManager->process($navShipmentData);

        $shipmentData = $this->dataObjectFactory->create(array_merge(
            [
                ShipmentInterface::ITEMS  => $shipmentItems,
                ShipmentInterface::TRACKS => [$navTrackData],
            ],
            $this->navShipmentPackageDataManager->process($navShipmentData)
        ));

        $this->dataProcessorManager->process($order, $shipmentData);
    }

    protected function fetchNavShipments($orderIncrementId)
    {
        return $this->postedPackageService->process(
            new ServiceRequest(
                new ReadOperation(),
                new MultipleDimension(),
                new FilterParameters(
                    new FilterGroup([
                        new SingleValueFilter($this->navFilterField, $orderIncrementId),
                    ])
                )
            )
        );
    }

    protected function getOrderItemCollectionMap(OrderInterface $order)
    {
        $orderItemCollectionMap = new CollectionMap(
            OrderItemInterface::ITEM_ID,
            0,
            new StaticCollectionProvider($order->getItemsCollection())
        );
        $orderItemCollectionMap->setPage(1);
        return $orderItemCollectionMap;
    }

    protected function getExistingShipmentsMap(OrderInterface $order)
    {
        $existingShipmentsMap = [];

        foreach ($order->getShipmentsCollection()->getItems() as $shipment) {
            $shipmentItems = $shipment->getItemsCollection();
            $existingShipmentsMap[$shipment->getId()] = array_combine(
                $shipmentItems->getColumnValues(ShipmentItemInterface::SKU),
                $shipmentItems->getColumnValues(ShipmentItemInterface::QTY)
            );
        }

        return $existingShipmentsMap;
    }
}
