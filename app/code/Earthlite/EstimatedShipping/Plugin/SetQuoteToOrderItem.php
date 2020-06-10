<?php
declare(strict_types = 1);
namespace Earthlite\EstimatedShipping\Plugin;

use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * class SetQuoteToOrderItem
 */
class SetQuoteToOrderItem
{
    /**
     * 
     * @param ToOrderItem $subject
     * @param \Closure $proceed
     * @param AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        ToOrderItem $subject,
        \Closure $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setShippingLeadTime($item->getShippingLeadTime());
        $orderItem->setItemType($item->getItemType());
        return $orderItem;
    }
}