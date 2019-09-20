<?php

/**
 * Customer Data Extractor
 */
namespace Creatuity\Nav\Model\Data\Mapping\Magento;

use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;
use Creatuity\Nav\Model\Data\Extractor\Magento\CustomerFieldDataExtractor;

/**
 * CustomerDataExtractorMapping
 */
class CustomerDataExtractorMapping
{
    /**
     * @var UnaryMapping 
     */
    protected $mapping;
    
    /**
     * @var CustomerFieldDataExtractor 
     */
    protected $customerDataExtractor;

    /**
     * 
     * @param UnaryMapping               $mapping 
     * @param CustomerFieldDataExtractor $customerDataExtractor 
     */
    public function __construct(
        UnaryMapping $mapping,
        CustomerFieldDataExtractor $customerDataExtractor
    ) {
        $this->mapping = $mapping;
        $this->customerDataExtractor = $customerDataExtractor;
    }

    /**
     * 
     * @param array $customerData 
     * 
     * @return array 
     */
    public function apply(array $customerData)
    {
        return [
            $this->mapping->get() => $this->customerDataExtractor->extract(
                $customerData
            ),
        ];
    }
}

