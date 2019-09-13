<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Sales\Api\Data\OrderItemInterface;

class OrderItemFieldOrderItemDataExtractor implements OrderItemDataExtractorInterface
{
    protected $accessorMethod;

    public function __construct($accessorMethod)
    {
        $this->accessorMethod = $accessorMethod;
    }

    public function extract(OrderItemInterface $orderItem)
    {
        return $orderItem->{$this->accessorMethod}();
    }
}
