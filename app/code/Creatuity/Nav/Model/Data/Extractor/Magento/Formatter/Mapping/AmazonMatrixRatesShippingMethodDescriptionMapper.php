<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento\Formatter\Mapping;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AmazonMatrixRatesShippingMethodDescriptionMapper
{
    protected $scopeConfig;
    protected $amazonMatrixRatesShippingMethodDescriptionMappingFactory;
    protected $mappingsConfigPath;
    protected $amazonMatrixRatesShippingMethodDescriptionMappings;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AmazonMatrixRatesShippingMethodDescriptionMappingFactory $amazonMatrixRatesShippingMethodDescriptionMappingFactory,
        $mappingsConfigPath
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->amazonMatrixRatesShippingMethodDescriptionMappingFactory = $amazonMatrixRatesShippingMethodDescriptionMappingFactory;
        $this->mappingsConfigPath = $mappingsConfigPath;
    }

    public function getMatrixRatesShippingMethodDescription($amazonShippingMethodDescription)
    {
        return $this->getMappedValue($amazonShippingMethodDescription, 'getAmazonShippingMethodDescription', 'getMatrixRatesShippingMethodDescription');
    }

    public function getAmazonShippingMethodDescription($matrixRatesShippingMethodDescription)
    {
        return $this->getMappedValue($matrixRatesShippingMethodDescription, 'getMatrixRatesShippingMethodDescription', 'getAmazonShippingMethodDescription');
    }

    protected function getMappedValue($fromValue, $fromFieldAccessor, $toFieldAccessor)
    {
        $this->parseConfig();

        foreach ($this->amazonMatrixRatesShippingMethodDescriptionMappings as $mapping) {
            if ($mapping->{$fromFieldAccessor}() === $fromValue) {
                return $mapping->{$toFieldAccessor}();
            }
        }

        throw new Exception($this->getExceptionMessage($fromValue, $fromFieldAccessor, $toFieldAccessor));
    }

    protected function getExceptionMessage($fromValue, $fromFieldAccessor, $toFieldAccessor)
    {
        $from = ($fromFieldAccessor === 'getMatrixRatesShippingMethodDescription') ? 'shipping method' : 'MatrixRates shipping method description';
        $to = ($toFieldAccessor === 'getMatrixRatesShippingMethodDescription') ? 'shipping method' : 'MatrixRates shipping method description';

        return "No {$from} is mapped for {$to} '{$fromValue}'";
    }

    protected function parseConfig()
    {
        if (isset($this->amazonMatrixRatesShippingMethodDescriptionMappings)) {
            return;
        }

        $mappingsData = explode("\n", $this->scopeConfig->getValue($this->mappingsConfigPath));
        $this->amazonMatrixRatesShippingMethodDescriptionMappings = [];
        foreach ($mappingsData as $row) {
            $rowData = explode(',', $row);
            $columnCount = count($rowData);
            if ($columnCount !== 2) {
                throw new Exception("Mapping data must use 2 column format, {$columnCount} column values provided");
            }
            $this->amazonMatrixRatesShippingMethodDescriptionMappings[] = $this->amazonMatrixRatesShippingMethodDescriptionMappingFactory->create(
                $this->getMappingData($rowData)
            );
        }
    }

    protected function getMappingData(array $rowData)
    {
        return [
            'amazonShippingMethodDescription'      => trim($rowData[0]),
            'matrixRatesShippingMethodDescription' => trim($rowData[1]),
        ];
    }
}
