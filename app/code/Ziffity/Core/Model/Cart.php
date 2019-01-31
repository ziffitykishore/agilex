<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ziffity\Core\Model;

/**
 * Shopping cart model
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @deprecated 100.1.0 Use \Magento\Quote\Model\Quote instead
 */
class Cart extends \Magento\Checkout\Model\Cart
{
     /**
     * Get shopping cart items count
     *
     * @return int
     * @codeCoverageIgnore
     */
    public function getItemsCount()
    {
        $items = $this->getQuote()->getAllVisibleItems();
        return count($items);
    }

    /**
     * Get shopping cart summary qty
     *
     * @return int|float
     * @codeCoverageIgnore
     */
    public function getItemsQty()
    {
        $items = $this->getQuote()->getAllVisibleItems();
        $qty = 0;
        foreach($items as $item) {
            $qty += $item->getQty();
        }
        return $qty;
    }
}

