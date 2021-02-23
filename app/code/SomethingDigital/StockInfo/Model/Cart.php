<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SomethingDigital\StockInfo\Model;

use Magento\AdvancedCheckout\Helper\Data;

class Cart extends \Magento\QuickOrder\Model\Cart
{   
    /**
     * Get result message.
     *
     * @param string $code
     * @return string
     */
    protected function getResultMessage($code)
    {   

        $message = $this->_checkoutData->getMessage($code);
        if (($message === '') && ($code === Data::ADD_ITEM_STATUS_FAILED_QTY_INCREMENTS)) {
            $message = __('');
        }
        if ($code === Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK) {
            $message = __('The SKU is out of stock.');
        }
        if ($code === Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED) {
            $message = __('We don\'t have the quantity you requested.');
        }

        return (string) $message;
    }

    /**
     * Is code type error.
     *
     * @param string $code
     * @return bool
     */
    protected function isError($code)
    {
        $allowedCodes = [
            Data::ADD_ITEM_STATUS_SUCCESS,
            Data::ADD_ITEM_STATUS_FAILED_CONFIGURE,
            Data::ADD_ITEM_STATUS_FAILED_QTY_INCREMENTS
        ];

        return (bool) !in_array($code, $allowedCodes);
    }
    
}
