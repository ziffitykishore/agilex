<?php

namespace SomethingDigital\ShipperHqCustomizations\Controller\DeliveryInfo;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;
use Psr\Log\LoggerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Catalog\Model\ProductRepository as ProductRepository;
use Magento\Checkout\Model\Cart;
use SomethingDigital\StockInfo\Model\Product\Attribute\Source\SxInventoryStatus;
use Magento\CatalogInventory\Model\Stock\Item;

class Index extends Action
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    protected $session;
    protected $productRepository;
    protected $cart;
    protected $stockItem;

    public function __construct(
        Context $context,
        ArrayManager $arrayManager,
        LoggerInterface $logger,
        SessionManagerInterface $coreSession,
        ProductRepository $productRepository,
        Cart $cart,
        Item $stockItem
    ) {
        $this->arrayManager = $arrayManager;
        $this->logger = $logger;
        $this->session = $coreSession;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->stockItem = $stockItem;
        parent::__construct($context);
    }

    public function execute()
    {
        $jsonResult = $this->resultFactory->create('json');
        $deliveryDates = $this->session->getItemsDeliveryDates();

        $deliveryInfo = [];
        $quoteItems = [];
        foreach ($this->cart->getQuote()->getAllVisibleItems() as $item) {
            $sku = $item->getSku();

            $product = $this->productRepository->get($sku);
            $sxInventory = $product->getData('sx_inventory_status');
            $stockItem = $this->stockItem->load($product->getId(), 'product_id');

            if ($sxInventory == SxInventoryStatus::STATUS_STOCK || $sxInventory == SxInventoryStatus::STATUS_DNR) {
                if (($stockItem->getQty() - $item->getQty() < 0)
                    && ($stockItem->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NOTIFY)
                ) {
                    $deliveryInfo[$sku] = __('Item on backorder');
                } elseif($deliveryDates) {
                    $deliveryInfo = $deliveryDates;
                }
            } elseif ($sxInventory == SxInventoryStatus::STATUS_ORDER_AS_NEEDED) {
               $deliveryInfo[$sku] = __('Ships direct from manufacturer');
            } else {
                $deliveryInfo[$sku] = '';
            }

        }

        $jsonResult->setData(
            [
                'status' => 'success',
                'code' => 200,
                'data' => $deliveryInfo
            ]
        );
        return $jsonResult;
    }

}
