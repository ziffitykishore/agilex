<?php
namespace Ziffity\Blockcustomers\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class OrderPlacebefore implements ObserverInterface
{
    
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getData();
        echo "<pre>";
        print_r($order);exit;
        //exit;
    }

}
