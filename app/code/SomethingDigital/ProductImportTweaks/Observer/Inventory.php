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
            if ($row['sx_inventory_status'] == 'DNR') {
                $row['stock.backorders'] = "No Backorders";
                $row['stock.use_config_manage_stock'] =  "No";
                $row['stock.manage_stock'] = "Yes";
            } elseif ($row['sx_inventory_status'] == 'Order as needed') {
                $row['stock.backorders'] = "No Backorders";
                $row['stock.use_config_manage_stock'] =  "No";
                $row['stock.is_in_stock'] = "In Stock";
                $row['stock.manage_stock'] = "No";
            } elseif ($row['sx_inventory_status'] == 'Stock') {
                $row['stock.backorders'] = "Allow Qty Below 0 and Notify Customer";
                $row['stock.use_config_manage_stock'] = "No";
                $row['stock.is_in_stock'] = "In Stock";
                $row['stock.manage_stock'] = "Yes";
            }
        }
        unset($row);
    }
}