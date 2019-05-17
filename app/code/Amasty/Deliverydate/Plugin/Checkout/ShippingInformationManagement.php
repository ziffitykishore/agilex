<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */


namespace Amasty\Deliverydate\Plugin\Checkout;

class ShippingInformationManagement
{
    /**
     * @var \Amasty\Deliverydate\Helper\Data
     */
    protected $amHelper;

    public function __construct(
        \Amasty\Deliverydate\Helper\Data $amHelper
    ) {
        $this->amHelper = $amHelper;
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
            $data = [
                'date'         => $extAttributes->getAmdeliverydateDate(),
                'tinterval_id' => $extAttributes->getAmdeliverydateTime(),
                'comment'      => $extAttributes->getAmdeliverydateComment()
            ];
            $this->amHelper->setDeliveryDataToSession($data);
        }

        return $proceed($cartId, $addressInformation);
    }
}
