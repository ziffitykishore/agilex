<?php

namespace Creatuity\Nav\Model\Data\Mapping\Magento;

use Magento\Sales\Api\Data\OrderItemInterface;
use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;
use Creatuity\Nav\Model\Data\Extractor\Magento\OrderItemDataExtractorInterface;

class OrderItemDataExtractorMapping
{
    protected $mapping;
    protected $orderItemDataExtractor;

    public function __construct(
        UnaryMapping $mapping,
        OrderItemDataExtractorInterface $orderItemDataExtractor
    ) {
        $this->mapping = $mapping;
        $this->orderItemDataExtractor = $orderItemDataExtractor;
    }

    public function apply(OrderItemInterface $orderItem)
    {
        return [
            $this->mapping->get() => $this->orderItemDataExtractor->extract($orderItem),
        ];
    }
}
