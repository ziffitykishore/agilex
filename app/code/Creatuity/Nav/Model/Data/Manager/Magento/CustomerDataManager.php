<?php

/**
 * Magento customer data provider
 */
namespace Creatuity\Nav\Model\Data\Manager\Magento;

/**
 * CustomerDataManager
 */
class CustomerDataManager
{
    /**
     * @var array
     */
    protected $dataExtractorMappings = [];

    /**
     * 
     * @param array $dataExtractorMappings 
     * @param array $staticDataExtractorMappings 
     */
    public function __construct(
        array $dataExtractorMappings = []
    ) {
        $this->dataExtractorMappings = $dataExtractorMappings;
    }

    /**
     * 
     * @param array $customerData 
     * 
     * @return array 
     */
    public function process(array $customerData)
    {
        $outputData = [];

        foreach ($this->dataExtractorMappings as $dataExtractorMapping) {
            $outputData = array_merge(
                $outputData,
                $dataExtractorMapping->apply($customerData)
            );
        }

        return $outputData;
    }
}

