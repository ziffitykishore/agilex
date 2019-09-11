<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

class StaticDataExtractor
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function extract()
    {
        return $this->value;
    }
}
