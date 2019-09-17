<?php

namespace Creatuity\Nav\Model\Data\Processor;

use Magento\Framework\DataObject;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Shipping\Model\ShipmentNotifier;

class ShipmentDataProcessor implements DataProcessorInterface
{
    protected $shipmentFactory;
    protected $shipmentNotifier;
    protected $transactionFactory;

    public function __construct(
        ShipmentFactory $shipmentFactory,
        ShipmentNotifier $shipmentNotifier,
        TransactionFactory $transactionFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->transactionFactory = $transactionFactory;
    }

    public function process(DataObject $orderData, DataObject $intermediateData)
    {
        $shipment = $this->shipmentFactory->create(
            $orderData,
            $intermediateData->getItems(),
            $intermediateData->getTracks()
        );

        if ($this->isShipmentEmpty($shipment)) {
            return;
        }

        $shipment->register();

        $shipment->getOrder()
            ->setIsInProcess(true)
            ->addStatusHistoryComment("Created Magento Shipment for NAV Package <b>{$intermediateData->getPackageId()}</b>")
        ;

        $this->transactionFactory->create()
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save()
        ;

        $this->shipmentNotifier->notify($shipment);
    }

    protected function isShipmentEmpty(ShipmentInterface $shipment)
    {
        $items = $shipment->getAllItems();
        $itemCount = count($items);
        if ($itemCount === 0) {
            return true;
        }

        $emptyCount = 0;
        foreach ($items as $item) {
            if ($item->getQty() == 0) {
                ++$emptyCount;
            }
        }

        return $emptyCount === $itemCount;
    }
}
