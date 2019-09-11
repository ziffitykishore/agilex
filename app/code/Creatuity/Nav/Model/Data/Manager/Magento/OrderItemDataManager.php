<?php

namespace Creatuity\Nav\Model\Data\Manager\Magento;

use Magento\Sales\Api\Data\OrderItemInterface;
use Creatuity\Nav\Model\Data\Mapping\Magento\OrderItemDataExtractorMapping;
use Creatuity\Nav\Model\Data\Mapping\Magento\StaticDataExtractorMapping;

class OrderItemDataManager
{
    protected $filters = [];
    protected $dataExtractorMappings = [];
    protected $staticDataExtractorMappings = [];

    public function __construct(
        array $filters = [],
        array $dataExtractorMappings = [],
        array $staticDataExtractorMappings = []
    ) {
        $this->filters = $filters;
        $this->dataExtractorMappings = $dataExtractorMappings;
        $this->staticDataExtractorMappings = $staticDataExtractorMappings;
    }

    public function addDataExtractorMapping(OrderItemDataExtractorMapping $dataExtractorMapping)
    {
        $this->dataExtractorMappings[] = $dataExtractorMapping;
    }

    public function addStaticDataExtractorMapping(StaticDataExtractorMapping $staticDataExtractorMapping)
    {
        $this->staticDataExtractorMappings[] = $staticDataExtractorMapping;
    }

    public function process(OrderItemInterface $orderItem)
    {
        $outputData = [];

        foreach ($this->dataExtractorMappings as $dataExtractorMapping) {
            $outputData = array_merge($outputData, $dataExtractorMapping->apply($orderItem));
        }

        foreach ($this->staticDataExtractorMappings as $staticDataExtractorMapping) {
            $outputData = array_merge($outputData, $staticDataExtractorMapping->apply());
        }

        // TODO: store transforms in this class and apply them here

        return $outputData;
    }
}
