<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderDataExtractorInterface
{
    public function extract(OrderInterface $order);
}
