<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Sales\Api\Data\OrderInterface;

class OrderFieldOrderDataExtractor implements OrderDataExtractorInterface
{
    protected $accessorMethod;

    public function __construct($accessorMethod)
    {
        $this->accessorMethod = $accessorMethod;
    }

    public function extract(OrderInterface $order)
    {
        return $order->{$this->accessorMethod}();
    }
}
