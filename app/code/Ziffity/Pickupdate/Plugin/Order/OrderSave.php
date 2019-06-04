<?php

namespace Ziffity\Pickupdate\Plugin\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderSave
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

    /**
     * Validate Order Pickup Date before place order
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface      $order
     *
     * @return OrderInterface
     */
    public function beforeSave(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $data = $this->amHelper->getPickupDataFromSession();
        if (is_array($data)) {
            /** @var \Ziffity\Pickupdate\Model\Pickupdate $pickupDate */
            $pickupDate = $this->pickupdateFactory->create();
            $pickupDate->prepareForSave($data, $order);
            $pickupDate->validate($order);
        }

        return [$order];
    }

    /**
     * Save Order Pickup Date from session
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface      $order
     *
     * @return OrderInterface
     */
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
