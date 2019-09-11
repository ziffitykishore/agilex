<?php

namespace Creatuity\Nav\Model\Data\Mapping\Magento;

use Magento\Sales\Api\Data\OrderInterface;
use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;
use Creatuity\Nav\Model\Data\Extractor\Magento\OrderDataExtractorInterface;

class OrderDataExtractorMapping implements OrderDataExtractorMappingInterface
{
    protected $mapping;
    protected $orderDataExtractor;

    public function __construct(
        UnaryMapping $mapping,
        OrderDataExtractorInterface $orderDataExtractor
    ) {
        $this->mapping = $mapping;
        $this->orderDataExtractor = $orderDataExtractor;
    }

    public function apply(OrderInterface $order)
    {
        return [
            $this->mapping->get() => $this->orderDataExtractor->extract($order),
        ];
    }
}
