<?php

namespace Earthlite\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;

class MarkBackOrderOnProductSave implements ObserverInterface {

    public function __construct(
    \Magento\CatalogInventory\Model\Stock\ItemFactory $itemFactory
    ) {
        $this->itemFactory = $itemFactory;
    }

    /**
     *  It will work with default Stock if we have multiple stock management code need to be modified
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $_product = $observer->getProduct();
        $id = $_product->getId();
        $stockItem = $this->itemFactory->create();
        $stock = $stockItem->load($id, 'product_id');
        if (!$_product->getProductionItem()) {
            $stock->setBackOrders(0);
            $stock->setUseConfigBackorders(0);
        } else {
            $stock->setBackOrders(1);
            $stock->setUseConfigBackorders(0);
        }
        $stock->save();
    }

}
