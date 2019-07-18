<?php

namespace Ziffity\Shipment\Plugin;

use Magento\Framework\App\ObjectManager;
use Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface;
use Magento\InventorySourceSelectionApi\Model\GetInventoryRequestFromOrder;
use Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface;
use Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface;
use Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Traversable;

class GetSourceSelectionResultFromInvoice
{
    
    /**
     * @var GetSkuFromOrderItemInterface
     */
    private $getSkuFromOrderItem;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * @var GetDefaultSourceSelectionAlgorithmCodeInterface
     */
    private $getDefaultSourceSelectionAlgorithmCode;

    /**
     * @var SourceSelectionServiceInterface
     */
    private $sourceSelectionService;

    /**
     * @var GetInventoryRequestFromOrder
     */
    private $getInventoryRequestFromOrder;

    /**
     * GetSourceSelectionResultFromInvoice constructor.
     *
     * @param GetSkuFromOrderItemInterface $getSkuFromOrderItem
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param null $stockByWebsiteIdResolver @deprecated
     * @param null $inventoryRequestFactory @deprecated
     * @param GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode
     * @param SourceSelectionServiceInterface $sourceSelectionService
     * @param GetInventoryRequestFromOrder|null $getInventoryRequestFromOrder
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        GetSkuFromOrderItemInterface $getSkuFromOrderItem,
        ItemRequestInterfaceFactory $itemRequestFactory,
        $stockByWebsiteIdResolver,
        $inventoryRequestFactory,
        GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode,
        SourceSelectionServiceInterface $sourceSelectionService,
        GetInventoryRequestFromOrder $getInventoryRequestFromOrder = null
    ) {
        $this->itemRequestFactory = $itemRequestFactory;
        $this->getDefaultSourceSelectionAlgorithmCode = $getDefaultSourceSelectionAlgorithmCode;
        $this->sourceSelectionService = $sourceSelectionService;
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
        $this->getInventoryRequestFromOrder = $getInventoryRequestFromOrder ?:
            ObjectManager::getInstance()->get(GetInventoryRequestFromOrder::class);
    }
    
    public function aroundExecute(
        \Magento\InventoryShipping\Model\GetSourceSelectionResultFromInvoice $subject,
        \Closure $proceed,
        InvoiceInterface $invoice
    ){  
        $order = $invoice->getOrder();
        $inventoryRequest = $this->getInventoryRequestFromOrder->execute(
            (int) $order->getEntityId(),
            $this->getSelectionRequestItems($invoice->getItems())
        );

        $selectionAlgorithmCode = $this->getDefaultSourceSelectionAlgorithmCode->execute();
        return $this->sourceSelectionService->execute($inventoryRequest, $selectionAlgorithmCode);
    }
    
    private function getSelectionRequestItems(iterable $invoiceItems): array
    {
        $selectionRequestItems = [];
        foreach ($invoiceItems as $invoiceItem) {
            $orderItem = $invoiceItem->getOrderItem();

            $itemSku = $this->getSkuFromOrderItem->execute($orderItem);
            $qty = $this->castQty($invoiceItem->getOrderItem(), $invoiceItem->getQty());

            $selectionRequestItems[] = $this->itemRequestFactory->create([
                'sku' => $itemSku,
                'qty' => $qty,
            ]);
        }
        return $selectionRequestItems;
    }    
    
    private function castQty(OrderItemInterface $item, $qty): float
    {
        if ($item->getIsQtyDecimal()) {
            $qty = (float) $qty;
        } else {
            $qty = (int) $qty;
        }

        return $qty > 0 ? $qty : 0;
    }    
}
