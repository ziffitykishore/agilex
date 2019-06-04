<?php

namespace Ziffity\PickupdateOR\Observer\Order\Place;

use Ziffity\Pickupdate\Observer\Order\Place\Before as PlaceOrderBefore;

class Before extends PlaceOrderBefore
{

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $helper;

    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateFactory
     */
    private $pickupdateFactory;

    public function __construct(
        \Ziffity\Pickupdate\Helper\Data $helper,
        \Ziffity\Pickupdate\Model\PickupdateFactory $pickupdateFactory
    ) {
        $this->helper = $helper;
        $this->pickupdateFactory = $pickupdateFactory;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $data = $this->helper->getPickupDataFromSession();
        if (is_array($data)) {
            $order = $observer->getOrder();
            /** @var \Ziffity\Pickupdate\Model\Pickupdate $pickupDate */
            $pickupDate = $this->pickupdateFactory->create();
            $pickupDate->prepareForSave($data, $order);
            $pickupDate->validatePickup($data, $order);
        }

        return $this;
    }    
	
}