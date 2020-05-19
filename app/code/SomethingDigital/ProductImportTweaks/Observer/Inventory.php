<?php
namespace SomethingDigital\ProductImportTweaks\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class Inventory implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $vars = $observer->getEvent()->getVars();
        foreach ($vars['new_data'] as &$row) {
            if (isset($row['sx_inventory_status'])) {
                if (strtolower(trim($row['sx_inventory_status'])) == 'dnr') {
                    $row['stock.backorders'] = "No Backorders";
                    $row['stock.use_config_manage_stock'] =  "No";
                    $row['stock.manage_stock'] = "Yes";
                } elseif (strtolower(trim($row['sx_inventory_status'])) == 'order as needed') {
                    $row['stock.backorders'] = "No Backorders";
                    $row['stock.use_config_manage_stock'] =  "No";
                    $row['stock.is_in_stock'] = "In Stock";
                    $row['stock.manage_stock'] = "No";
                } elseif (strtolower(trim($row['sx_inventory_status'])) == 'stock') {
                    $row['stock.backorders'] = "Allow Qty Below 0 and Notify Customer";
                    $row['stock.use_config_manage_stock'] = "No";
                    $row['stock.is_in_stock'] = "In Stock";
                    $row['stock.manage_stock'] = "Yes";
                }
            }
        }
        unset($row);
    }
}