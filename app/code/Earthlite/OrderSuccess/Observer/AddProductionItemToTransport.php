<?php
declare(strict_types = 1);
namespace Earthlite\OrderSuccess\Observer;

use Magento\Framework\Event\ObserverInterface;
use Earthlite\LateOrders\Model\LateOrders;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * class AddProductionItemToTransport
 */
class AddProductionItemToTransport implements ObserverInterface
{  
    /**
     * 
     * @param LateOrders $lateOrders
     */
    public function __construct(
      LateOrders $lateOrders,
      DateTime $dateTime
    ) {
        $this->lateOrders = $lateOrders;
        $this->dateTime = $dateTime;
    }

    /**
     * 
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productionItemExist = false;
        $transport = $observer->getEvent()->getTransport();
        $order = $transport['order'];
        $modifiedLeadDatesofOrderItems = [];
        foreach ($order->getAllVisibleItems() as $item) {
            
            $leadTime = $item->getShippingLeadTime();
            
            $modifiedLeadDatesofOrderItems[] = $this->lateOrders->formatLeadDate($leadTime, $order->getCreatedAt());
        }
        $transport['is_production'] = $productionItemExist;
        if ($order->getShippingMethod(true)->getMethod() == 'GROUND') {
            $transport['ground_method'] = true;
        }
        if ($order->getShippingMethod(true)->getMethod() == 'Freight') {
            $transport['freight_method'] = true;
        }
        $transport['estimated_date'] = $this->getEstimatedDeliveryTime($modifiedLeadDatesofOrderItems);
    }
    
    /**
     * 
     * @param type $modifiedLeadDatesofOrderItems
     * @return string
     */
    protected function getEstimatedDeliveryTime($modifiedLeadDatesofOrderItems)
    {
         return  $maxLeadTime = $this->dateTime->gmtDate('M d, Y',
                max($modifiedLeadDatesofOrderItems)
         );
    }
}
