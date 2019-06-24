<?php

namespace Ziffity\Pickupdate\Model;

use Ziffity\Pickupdate\Model\PickupdateRepository;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\Order;

class LoaderExtensions
{
    /**
     * @var PickupdateRepository
     */
    private $pickupDateRepository;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    public function __construct(
        PickupdateRepository $pickupdateRepository,
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->pickupDateRepository = $pickupdateRepository;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @param Order $order
     */
    public function loadPickupDateExtensionAttributes(Order $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }
        if ($extensionAttributes->getPickupdateDate() !== null) {
            // Pickup Date entity is already loaded; no actions required
            return;
        }
        try {
            $pickupDate = $this->pickupDateRepository->getByOrder($order->getEntityId());

            $extensionAttributes->setPickupdateDate($pickupDate->getDate());
            $extensionAttributes->setPickupdateTime($pickupDate->getTime());
            $extensionAttributes->setPickupdateComment($pickupDate->getComment());

            $order->setExtensionAttributes($extensionAttributes);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // Pickup Date entity cannot be loaded for current order; no actions required
            return;
        }
    }
}
