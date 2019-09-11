<?php

namespace Creatuity\Nav\Model\Data\Manager\Magento\Filter;

use Magento\Sales\Api\Data\OrderItemInterface;

interface OrderItemFilterInterface
{
    public function isFiltered(OrderItemInterface $orderItem);
}
