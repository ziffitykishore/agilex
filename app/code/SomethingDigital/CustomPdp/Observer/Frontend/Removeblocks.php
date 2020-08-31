<?php
 
namespace SomethingDigital\CustomPdp\Observer\Frontend;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry as CoreRegistry;
use SomethingDigital\StockInfo\Model\Product\Attribute\Source\SxInventoryStatus;
use \Magento\CatalogInventory\Model\Stock\Item;
 
class Removeblocks implements ObserverInterface
{
    /**
     * @var CoreRegistry
     */
    private $coreRegistry;

    /**
     * @var StockItem
     */
    protected $stockItem;

    public function __construct(
        CoreRegistry $coreRegistry,
        Item $stockItem
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->stockItem = $stockItem;
    }
    /*
     * Hide MSRP Price, Final Price and Master catalog link if DNR & Zero Stock 
    */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->isZeroDNR()) {
            $layout = $observer->getLayout();
            $layout->unsetElement('somethingdigital_custompdp_msrp_price');
            $layout->unsetElement('product.page.number');
            $layout->unsetElement('product.price.final');
        }
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Check if DNR & Zero Stock
     *
     * @return string
     */
    public function isZeroDNR()
    {
        $product = $this->getProduct();
        $sxInventoryStatus = $product->getData('sx_inventory_status');
        $stockItem = $this->stockItem->load($product->getId(), 'product_id');
        $isZeroDNR = false;
        if ($sxInventoryStatus == SxInventoryStatus::STATUS_DNR && $stockItem->getQty() == 0) {
            $isZeroDNR = true;
        }
        return $isZeroDNR;
    }
}