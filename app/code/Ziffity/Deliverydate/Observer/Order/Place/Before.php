<?php

namespace Ziffity\Deliverydate\Observer\Order\Place;

use Amasty\Deliverydate\Observer\Order\Place\Before as PlaceOrderBefore;

class Before extends PlaceOrderBefore
{

    /**
     * @var \Amasty\Deliverydate\Helper\Data
     */
    private $amHelper;

    /**
     * @var \Amasty\Deliverydate\Model\DeliverydateFactory
     */
    private $deliverydateFactory;

    public function __construct(
        \Amasty\Deliverydate\Helper\Data $amHelper,
        \Amasty\Deliverydate\Model\DeliverydateFactory $deliverydateFactory
    ) {
        $this->amHelper = $amHelper;
        $this->deliverydateFactory = $deliverydateFactory;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $data = $this->amHelper->getDeliveryDataFromSession();
        if (is_array($data)) {
            $order = $observer->getOrder();
            /** @var \Amasty\Deliverydate\Model\Deliverydate $deliveryDate */
            $deliveryDate = $this->deliverydateFactory->create();
            if ($deliveryDate->isDelivery()) {
                $deliveryDate->prepareForSave($data, $order);
                $deliveryDate->validateDelivery($data, $order);
            }
        }
        return $this;
    }    
	
}