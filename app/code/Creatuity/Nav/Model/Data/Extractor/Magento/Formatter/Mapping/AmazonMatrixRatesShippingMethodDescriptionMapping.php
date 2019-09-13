<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento\Formatter\Mapping;

class AmazonMatrixRatesShippingMethodDescriptionMapping
{
    protected $amazonShippingMethodDescription;
    protected $matrixRatesShippingMethodDescription;

    public function __construct(
        $amazonShippingMethodDescription,
        $matrixRatesShippingMethodDescription
    )
    {
        $this->amazonShippingMethodDescription = $amazonShippingMethodDescription;
        $this->matrixRatesShippingMethodDescription = $matrixRatesShippingMethodDescription;
    }

    public function getAmazonShippingMethodDescription()
    {
        return $this->amazonShippingMethodDescription;
    }

    public function getMatrixRatesShippingMethodDescription()
    {
        return $this->matrixRatesShippingMethodDescription;
    }
}
