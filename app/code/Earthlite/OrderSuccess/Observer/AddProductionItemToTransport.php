<?php
declare(strict_types = 1);
namespace Earthlite\OrderSuccess\Observer;

use Magento\Framework\Event\ObserverInterface;
use Earthlite\LateOrders\Model\LateOrders;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

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
      DateTime $dateTime,
      LoggerInterface $logger
    ) {
        $this->lateOrders = $lateOrders;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * 
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $productionItemExist = false;
            $transport = $observer->getEvent()->getTransport();
            $order = $transport['order'];
            $modifiedLeadDatesofOrderItems = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $leadTime = $item->getShippingLeadTime();
                if ($leadTime) {
                    $type = $item->getItemType();
                    $modifiedLeadDatesofOrderItems[] = $this->lateOrders->formatLeadDate($leadTime, $order, $type);
                }
                if ($type == 1) {
                    $productionItemExist = true;
                }
            }
            $transport['is_production'] = $productionItemExist;
            if ($order->getShippingMethod(true)->getMethod() == 'FEDEX_GROUND') {
                $transport['ground_method'] = true;
            }
            if ($order->getShippingMethod(true)->getMethod() == 'FEDEX_FREIGHT') {
                $transport['freight_method'] = true;
            }
            $transport['estimated_date'] = $this->dateTime->date(
                    'Y-m-d', strtotime('2 days', strtotime($order->getCreatedAt()))
            );
            if (!empty($modifiedLeadDatesofOrderItems)) {
                $transport['estimated_date'] = $this->getEstimatedDeliveryTime($modifiedLeadDatesofOrderItems);
            }

        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
    
    /**
     * 
     * @param type $modifiedLeadDatesofOrderItems
     * @return string
     */
    protected function getEstimatedDeliveryTime($modifiedLeadDatesofOrderItems)
    {
        return $this->dateTime->gmtDate('M d, Y', max($modifiedLeadDatesofOrderItems));
    }
}
