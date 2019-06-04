<?php

namespace Ziffity\PickupdateOR\Plugin\Order;

use Ziffity\Pickupdate\Plugin\Order\OrderSave as OrderSavePlugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderSave extends OrderSavePlugin
{

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $helper;

    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateFactory
     */
    private $pickupdateFactory;

    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate
     */
    private $pickupdateResource;

    public function __construct(
        \Ziffity\Pickupdate\Helper\Data $helper,
        \Ziffity\Pickupdate\Model\PickupdateFactory $pickupdateFactory,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResource
    ) {
        $this->helper = $helper;
        $this->pickupdateFactory = $pickupdateFactory;
        $this->pickupdateResource = $pickupdateResource;
    }
    
    
    public function beforeSave(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $data = $this->helper->getPickupDataFromSession();
        if (is_array($data)) {
            /** @var \Ziffity\Pickupdate\Model\Pickupdate $pickupDate */
            $pickupDate = $this->pickupdateFactory->create();
            $pickupDate->prepareForSave($data, $order);
            $pickupDate->validatePickup($data, $order);
        }
        return [$order];
    }

    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        
        $data = $this->helper->getPickupDataFromSession();
        if (is_array($data)) {
            /** @var \Ziffity\Pickupdate\Model\Pickupdate $pickupDate */
            $pickupDate = $this->pickupdateFactory->create();
            if ($pickupDate->prepareForSave($data, $order)) {
                $this->pickupdateResource->save($pickupDate);
            }
        }
        return $order;
    }    
}