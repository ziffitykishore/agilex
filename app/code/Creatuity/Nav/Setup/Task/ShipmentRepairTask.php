<?php

namespace Creatuity\Nav\Setup\Task;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Creatuity\Nav\Model\Task\ShipmentUpdateTask;
use Creatuity\Nav\Model\Map\CollectionMap;
use Creatuity\Nav\Model\Data\Processor\Manager\DataProcessorManager;
use Creatuity\Nav\Model\Data\Manager\Nav\DataManager;
use Creatuity\Nav\Model\Service\Service;

class ShipmentRepairTask extends ShipmentUpdateTask
{
    protected $shipmentRepository;
    protected $orderItemRepository;

    public function __construct(
        LoggerInterface $logger,
        $navFilterField,
        CollectionMap $orderCollectionMap,
        Service $postedPackageService,
        DataManager $navShipmentTrackDataManager,
        DataManager $navShipmentItemDataManager,
        DataManager $navShipmentPackageDataManager,
        DataObjectFactory $dataObjectFactory,
        DataProcessorManager $dataProcessorManager,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderItemRepositoryInterface $orderItemRepository
    ) {
        parent::__construct(
            $logger,
            $navFilterField,
            $orderCollectionMap,
            $postedPackageService,
            $navShipmentTrackDataManager,
            $navShipmentItemDataManager,
            $navShipmentPackageDataManager,
            $dataObjectFactory,
            $dataProcessorManager
        );

        $this->shipmentRepository = $shipmentRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    protected function updateShipments(OrderInterface $order, $orderIncrementId)
    {
        try {
            if (!$order->hasShipments()) {
                return;
            }

            $navShipments = $this->fetchNavShipments($orderIncrementId);
            if (count($navShipments) > 1) {
                throw new Exception("Can't automatically repair order '{$orderIncrementId}' because more than one NAV shipment exists.");
            }
            $navShipmentData = reset($navShipments);

            if (!isset($navShipmentData['Posted_Package_Line'])) {
                return;
            }

            $navShipmentItems = $navShipmentData['Posted_Package_Line']->Posted_Package_Line;
            if (!is_array($navShipmentItems)) {
                $navShipmentItems = [$navShipmentItems];
            }

            $navShipmentItemMap = [];
            foreach ($navShipmentItems as $navShipmentItem) {
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


            $existingShipmentsMap = $this->getExistingShipmentsMap($order);
            if (in_array($navShipmentItemMap, $existingShipmentsMap)) {
                return;
            }


            if ($order->getShipmentsCollection()->count() > 1) {
                throw new Exception("Can't automatically repair order '{$orderIncrementId}' because more than one Magento shipment exists.");
            }
            $existingShipment = $order->getShipmentsCollection()->getFirstItem();
            $this->shipmentRepository->delete($existingShipment);

            $orderItemCollectionMap = $this->getOrderItemCollectionMap($order);
            foreach ($orderItemCollectionMap->getPageIndices() as $pageIndex) {
                $orderItemCollectionMap->setPage($pageIndex);

                foreach ($orderItemCollectionMap->getKeys() as $orderItemId) {
                    $orderItem = $orderItemCollectionMap->get($orderItemId);
                    $orderItem->setQtyShipped(0);
                    $this->orderItemRepository->save($orderItem);
                }
            }
        } catch (Exception $exception) {
            $this->logger->debug($exception);
        }
    }
}
