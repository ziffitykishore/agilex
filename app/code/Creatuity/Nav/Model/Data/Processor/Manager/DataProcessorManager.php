<?php

namespace Creatuity\Nav\Model\Data\Processor\Manager;

use Magento\Framework\DataObject;

class DataProcessorManager
{
    protected $dataProcessors = [];

    public function __construct(
        array $dataProcessors = []
    ) {
        $this->dataProcessors = $dataProcessors;
    }

    public function process(DataObject $productData, DataObject $intermediateData)
    {
        foreach ($this->dataProcessors as $dataProcessor) {
            $dataProcessor->process($productData, $intermediateData);
        }
    }
}
