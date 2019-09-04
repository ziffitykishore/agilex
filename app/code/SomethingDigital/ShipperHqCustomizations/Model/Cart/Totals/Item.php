<?php
namespace SomethingDigital\ShipperHqCustomizations\Model\Cart\Totals;

class Item extends \Magento\Quote\Model\Cart\Totals\Item
{
    public function getSku()
    {
        return $this->_get('sku');
    }

    public function setSku($sku)
    {
        return $this->setData('sku', $sku);
    }
}