<?php

namespace Creatuity\Nav\Model\Data\Manager\Nav;

use Creatuity\Nav\Model\Data\Mapping\Nav\DataMapping;

class DataManager
{
    protected $dataMappings = [];

    public function __construct(array $dataMappings = [])
    {
        $this->dataMappings = $dataMappings;
    }

    public function addDataMapping(DataMapping $dataMapping)
    {
        $this->dataMappings[] = $dataMapping;
    }

    public function process(array $inputData)
    {
        $outputData = [];

        foreach ($this->dataMappings as $dataMapping) {
            $outputData = array_merge($outputData, $dataMapping->apply($inputData));
        }

        return $outputData;
    }
}
