<?php

namespace Ziffity\Checkout\Api;

use Ziffity\Checkout\Api\Data\OrderInfoInterface;

/**
 * Interface for saving the checkout order Info
 * to the quote for logged in users
 * @api
 */
interface OrderInfoManagementInterface
{
    /**
     * @param int $cartId
     * @param OrderInfoInterface $orderInfo
     * @return string
     */
    public function saveStoreInfo(
        $cartId,
        OrderInfoInterface $orderInfo
    );
}
