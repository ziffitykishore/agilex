<?php

namespace Creatuity\Nav\Model\Data\Mapping\Magento;

use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;
use Creatuity\Nav\Model\Data\Extractor\Magento\StaticDataExtractor;

class StaticDataExtractorMapping
{
    protected $mapping;
    protected $staticDataExtractor;

    public function __construct(
        UnaryMapping $mapping,
        StaticDataExtractor $staticDataExtractor
    ) {
        $this->mapping = $mapping;
        $this->staticDataExtractor = $staticDataExtractor;
    }

    public function apply()
    {
        return [
            $this->mapping->get() => $this->staticDataExtractor->extract(),
        ];
    }
}
