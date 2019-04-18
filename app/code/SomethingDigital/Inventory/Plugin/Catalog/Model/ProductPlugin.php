<?php

namespace SomethingDigital\Inventory\Plugin\Catalog\Model;
 
class ProductPlugin
{
    public function beforeBeforeSave(\Magento\Catalog\Model\Product $subject)
    {
        $stockData = $subject->getStockData();
        if ($subject->getAttributeText('sx_inventory_status') == 'DNR') {
            $stockData['backorders'] = 0;
            $stockData['use_config_manage_stock'] = 0;
            $stockData['manage_stock'] = 1;
        } elseif ($subject->getAttributeText('sx_inventory_status') == 'Order as needed') {
            $stockData['backorders'] = 0;
            $stockData['use_config_manage_stock'] = 0;
            $stockData['is_in_stock'] = 1;
            $stockData['manage_stock'] = 0;
        } elseif ($subject->getAttributeText('sx_inventory_status') == 'Stock') {
            $stockData['backorders'] = 2;
            $stockData['use_config_manage_stock'] = 0;
            $stockData['is_in_stock'] = 1;
            $stockData['manage_stock'] = 1;
        }
        $subject->setStockData($stockData);
    }
}