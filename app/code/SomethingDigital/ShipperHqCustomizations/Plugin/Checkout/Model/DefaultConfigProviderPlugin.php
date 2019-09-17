<?php
namespace SomethingDigital\ShipperHqCustomizations\Plugin\Checkout\Model;
 
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Model\ProductRepository as ProductRepository;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\Checkout\Model\DefaultConfigProvider;
use SomethingDigital\StockInfo\Model\Product\Attribute\Source\SxInventoryStatus;
 
class DefaultConfigProviderPlugin extends \Magento\Framework\Model\AbstractModel
{
    protected $checkoutSession;
    protected $coreSession;
    protected $productRepository;
    protected $stockItem;
 
    public function __construct(
        CheckoutSession $checkoutSession,
        SessionManagerInterface $coreSession,
        ProductRepository $productRepository,
        Item $stockItem
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->session = $coreSession;
        $this->productRepository = $productRepository;
        $this->stockItem = $stockItem;
    }
 
    public function afterGetConfig(
        DefaultConfigProvider $subject, 
        array $result
    ) {
        $items = $result['totalsData']['items'];

        $deliveryDates = $this->session->getItemsDeliveryDates();

        foreach ($items as $index => $item) {

            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $product = $this->productRepository->getById($quoteItem->getProduct()->getId());

            $sxInventory = $product->getData('sx_inventory_status');
            $stockItem = $this->stockItem->load($product->getId(), 'product_id');


            if ($sxInventory == SxInventoryStatus::STATUS_STOCK || $sxInventory == SxInventoryStatus::STATUS_DNR) {
                if (($stockItem->getQty() - $item['qty'] < 0)
                    && ($stockItem->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NOTIFY)
                ) {
                    $deliveryInfo = 'Item on backorder';
                } else {
                    if (isset($deliveryDates[$product->getSku()])) {
                        $deliveryInfo = 'Expected Delivery: ' . $deliveryDates[$product->getSku()];
                    } else {
                        $deliveryInfo = '';
                    }
                }
            } elseif ($sxInventory == SxInventoryStatus::STATUS_ORDER_AS_NEEDED) {
               $deliveryInfo = 'Ships direct from manufacturer';
            }

            $result['quoteItemData'][$index]['deliveryInfo'] = $deliveryInfo;
        }
        return $result;
    }
}