<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento\Formatter;

class MatrixRateShippingMethodDescriptionDataExtractorFormatter extends AbstractShippingMethodDescriptionDataExtractorFormatter
{
    public function format()
    {
        return str_replace(
            "{$this->carrierTitle}{$this->descriptionDelimiter}",
            '',
            $this->shippingDescription
        );
    }
}
