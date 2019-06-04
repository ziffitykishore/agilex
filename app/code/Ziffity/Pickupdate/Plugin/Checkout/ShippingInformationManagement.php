<?php

namespace Ziffity\Pickupdate\Plugin\Checkout;

class ShippingInformationManagement
{
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;
    
    protected $deliveryHelper;

    public function __construct(
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Amasty\Deliverydate\Helper\Data $deliverHelper
    ) {
        $this->pickupHelper = $pickupHelper;
        $this->deliveryHelper = $deliverHelper;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        if ($extAttributes instanceof \Magento\Checkout\Api\Data\ShippingInformationExtension) {
            $pickupData = [
                'date'         => $extAttributes->getPickupdateDate(),
                'tinterval_id' => $extAttributes->getPickupdateTime(),
                'comment'      => $extAttributes->getPickupdateComment()
            ];
            $deliveryData = [
                'date'         => $extAttributes->getAmdeliverydateDate(),
                'tinterval_id' => $extAttributes->getAmdeliverydateTime(),
                'comment'      => $extAttributes->getAmdeliverydateComment()
            ];
            $this->pickupHelper->setPickupDataToSession($pickupData);
            $this->deliveryHelper->setDeliveryDataToSession($deliveryData);
        }

        return $proceed($cartId, $addressInformation);
    }
}
