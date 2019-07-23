<?php

namespace Ziffity\Shipment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $convertOrder;
    
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    public function __construct(
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction            
    ) {
        $this->convertOrder = $convertOrder;
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;        
    }


    public function execute(Observer $observer) 
    {    
        $order = $observer->getEvent()->getOrder();
        
        if($order->getIsVirtual()) {

            $orderId = $order->getId();
            $order = $this->_orderRepository->get($orderId);

            if($order->canInvoice()) {
                $invoice = $this->_invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->_transaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
            }

            return ;
        }
        // Check if order can be shipped or has already shipped
        if (! $order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t create an shipment.')
            );
        }
        // Initialize the order shipment object
        $shipment = $this->convertOrder->toShipment($order);

        foreach ($order->getAllItems() AS $orderItem) {
            // Check if order item has qty to ship or is virtual
            if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();
            // Create shipment item with qty
            $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            // Add shipment item to shipment
            $shipment->addItem($shipmentItem);
        }
        // Register shipment
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        $shipment->getExtensionAttributes()->setSourceCode('default');
        try {
            $shipment->save();
            $shipment->getOrder()->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }
}
