<?php
 
namespace Ziffity\Checkout\Api;

use Ziffity\Checkout\Api\Data\OrderInfoInterface;

/**
 * Interface for saving the checkout order Info
 * to the quote for guest users
 * @api
 */
interface GuestOrderInfoManagementInterface
{
    /**
     * @param string $cartId
     * @param OrderInfoInterface $orderInfo
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveStoreInfo(
        $cartId,
        OrderInfoInterface $orderInfo
    );
}
