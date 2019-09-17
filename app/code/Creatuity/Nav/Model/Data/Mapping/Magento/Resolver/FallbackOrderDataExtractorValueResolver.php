<?php

namespace Creatuity\Nav\Model\Data\Mapping\Magento\Resolver;

use Magento\Sales\Api\Data\OrderInterface;
use Creatuity\Nav\Model\Data\Extractor\Magento\OrderDataExtractorInterface;

class FallbackOrderDataExtractorValueResolver implements OrderDataExtractorValueResolverInterface
{
    protected $primaryOrderDataExtractor;
    protected $secondaryOrderDataExtractor;
    protected $valueFilter = [];
    protected $fallbackValue;

    public function __construct(
        OrderDataExtractorInterface $primaryOrderDataExtractor,
        OrderDataExtractorInterface $secondaryOrderDataExtractor,
        array $valueFilter = [],
        $fallbackValue
    ) {
        $this->primaryOrderDataExtractor = $primaryOrderDataExtractor;
        $this->secondaryOrderDataExtractor = $secondaryOrderDataExtractor;
        $this->valueFilter = $valueFilter;
        $this->fallbackValue = $fallbackValue;
    }

    public function resolve(OrderInterface $order)
    {
        $dataPrimary = $this->primaryOrderDataExtractor->extract($order);
        $dataSecondary = $this->secondaryOrderDataExtractor->extract($order);

        if (in_array($dataPrimary, $this->valueFilter)) {
            return $dataSecondary;
        }

        return $this->fallbackValue;
    }
}
