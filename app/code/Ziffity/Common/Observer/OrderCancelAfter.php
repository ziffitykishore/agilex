<?php

namespace Ziffity\Common\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Trigger an action after canceling an order
 */
class OrderCancelAfter implements ObserverInterface
{

    /**
     * @var \Amasty\Deliverydate\Model\DeliverydateFactory
     */
    protected $deliveryDateFactory;

    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateFactory
     */
    protected $pickupDateFactory;

    /**
     * @param \Amasty\Deliverydate\Model\DeliverydateFactory $deliveryDateFactory
     * @param \Ziffity\Pickupdate\Model\PickupdateFactory $pickupDateFactory
     */
    public function __construct(
        \Amasty\Deliverydate\Model\DeliverydateFactory $deliveryDateFactory,
        \Ziffity\Pickupdate\Model\PickupdateFactory $pickupDateFactory
    ) {
        $this->deliveryDateFactory = $deliveryDateFactory;
        $this->pickupDateFactory = $pickupDateFactory;
    }
 
    /**
     * Release time slots reserved by order.
     * 
     * @return null
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrder()->getId();
        $delivery = $this->deliveryDateFactory->create()->load($orderId,'order_id');
        $pickup = $this->pickupDateFactory->create()->load($orderId,'order_id');
        
        if ($delivery->getActive() != null && $delivery->getActive())
        {
            $delivery->setActive(0);
            $delivery->save();
        }

        if ($pickup->getActive() != null && $pickup->getActive())
        {
            $pickup->setActive(0);
            $pickup->save();
        }
    }
}