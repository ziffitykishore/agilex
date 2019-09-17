<?php

namespace Creatuity\Nav\Model\Data\Manager\Magento;

use Magento\Sales\Api\Data\OrderInterface;
use Creatuity\Nav\Model\Data\Mapping\Magento\OrderDataExtractorMapping;
use Creatuity\Nav\Model\Data\Mapping\Magento\StaticDataExtractorMapping;

class OrderDataManager
{
    protected $dataExtractorMappings = [];
    protected $staticDataExtractorMappings = [];

    public function __construct(
        array $dataExtractorMappings = [],
        array $staticDataExtractorMappings = []
    ) {
        $this->dataExtractorMappings = $dataExtractorMappings;
        $this->staticDataExtractorMappings = $staticDataExtractorMappings;
    }

    public function addDataExtractorMapping(OrderDataExtractorMapping $dataExtractorMapping)
    {
        $this->dataExtractorMappings[] = $dataExtractorMapping;
    }

    public function addStaticDataExtractorMapping(StaticDataExtractorMapping $staticDataExtractorMapping)
    {
        $this->staticDataExtractorMappings[] = $staticDataExtractorMapping;
    }

    public function process(OrderInterface $order)
    {
        $outputData = [];

        foreach ($this->dataExtractorMappings as $dataExtractorMapping) {
            $outputData = array_merge($outputData, $dataExtractorMapping->apply($order));
        }

        foreach ($this->staticDataExtractorMappings as $staticDataExtractorMapping) {
            $outputData = array_merge($outputData, $staticDataExtractorMapping->apply());
        }

        return $outputData;
    }
}
