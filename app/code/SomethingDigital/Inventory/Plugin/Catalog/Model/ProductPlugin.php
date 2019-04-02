<?php
namespace SomethingDigital\Inventory\Plugin\Catalog\Model;
 
class ProductPlugin
{
    public function beforeBeforeSave(\Magento\Catalog\Model\Product $subject)
    {
        $stockData = [];
        if ($subject->getSxInventoryStatus() == 1) {
            $stockData = [
                'backorders' => 0,
                'use_config_manage_stock' => 0,
                'manage_stock' => 1
            ];
        } elseif ($subject->getSxInventoryStatus() == 2) {
            $stockData = [
                'backorders' => 0,
                'use_config_manage_stock' => 0,
                'is_in_stock' => 1,
                'manage_stock' => 0
            ];
        } elseif ($subject->getSxInventoryStatus() == 3) {
            $stockData = [
                'backorders' => 2,
                'use_config_manage_stock' => 0,
                'is_in_stock' => 1,
                'manage_stock' => 1
            ];
        }
        $subject->setStockData($stockData);
    }
}