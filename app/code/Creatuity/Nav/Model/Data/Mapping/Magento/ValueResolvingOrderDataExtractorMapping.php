<?php

namespace Creatuity\Nav\Model\Data\Mapping\Magento;

use Creatuity\Nav\Model\Data\Mapping\Magento\Resolver\FallbackOrderDataExtractorValueResolver;
use Magento\Sales\Api\Data\OrderInterface;
use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;

class ValueResolvingOrderDataExtractorMapping implements OrderDataExtractorMappingInterface
{
    protected $mapping;
    protected $orderDataExtractorValueResolver;

    public function __construct(
        UnaryMapping $mapping,
        FallbackOrderDataExtractorValueResolver $orderDataExtractorValueResolver
    ) {
        $this->mapping = $mapping;
        $this->orderDataExtractorValueResolver = $orderDataExtractorValueResolver;
    }

    public function apply(OrderInterface $order)
    {
        return [
            $this->mapping->get() => $this->orderDataExtractorValueResolver->resolve($order),
        ];
    }
}
