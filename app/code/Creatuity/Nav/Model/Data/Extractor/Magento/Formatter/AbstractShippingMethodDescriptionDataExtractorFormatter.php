<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento\Formatter;

abstract class AbstractShippingMethodDescriptionDataExtractorFormatter implements DataExtractorFormatterInterface
{
    protected $carrierTitle;
    protected $shippingDescription;
    protected $descriptionDelimiter;

    public function __construct(
        $carrierTitle,
        $shippingDescription,
        $descriptionDelimiter = ' - '
    )
    {
        $this->carrierTitle = $carrierTitle;
        $this->shippingDescription = $shippingDescription;
        $this->descriptionDelimiter = $descriptionDelimiter;
    }
}
