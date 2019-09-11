<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Sales\Api\Data\OrderItemInterface;

interface OrderItemDataExtractorInterface
{
    public function extract(OrderItemInterface $orderItem);
}
