<?php

namespace Creatuity\Nav\Model\Data\Mapping\Magento\Resolver;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderDataExtractorValueResolverInterface
{
    public function resolve(OrderInterface $order);
}
