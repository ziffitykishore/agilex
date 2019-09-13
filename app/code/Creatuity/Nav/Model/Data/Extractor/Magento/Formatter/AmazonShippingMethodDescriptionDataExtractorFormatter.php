<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento\Formatter;

use Creatuity\Nav\Model\Data\Extractor\Magento\Formatter\Mapping\AmazonMatrixRatesShippingMethodDescriptionMapper;

class AmazonShippingMethodDescriptionDataExtractorFormatter extends AbstractShippingMethodDescriptionDataExtractorFormatter
{
    protected $amazonMatrixRatesShippingMethodDescriptionMapper;

    public function __construct(
        AmazonMatrixRatesShippingMethodDescriptionMapper $amazonMatrixRatesShippingMethodDescriptionMapper,
        $carrierTitle,
        $shippingDescription,
        $descriptionDelimiter = ' - '
    )
    {
        parent::__construct($carrierTitle, $shippingDescription, $descriptionDelimiter);

        $this->amazonMatrixRatesShippingMethodDescriptionMapper = $amazonMatrixRatesShippingMethodDescriptionMapper;
    }

    public function format()
    {
        $shippingDescriptionComponents = explode($this->descriptionDelimiter, $this->shippingDescription);
        $amazonMatrixRatesShippingMethodDescription = trim(reset($shippingDescriptionComponents));
        return $this->amazonMatrixRatesShippingMethodDescriptionMapper->getMatrixRatesShippingMethodDescription($amazonMatrixRatesShippingMethodDescription);
    }
}
