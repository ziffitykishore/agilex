<?php

namespace Creatuity\Nav\Model\Data\Mapping\Magento;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderDataExtractorMappingInterface
{
    public function apply(OrderInterface $order);
}
