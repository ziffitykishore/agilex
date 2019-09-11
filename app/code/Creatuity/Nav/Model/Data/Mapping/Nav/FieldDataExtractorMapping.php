<?php

namespace Creatuity\Nav\Model\Data\Mapping\Nav;

use Creatuity\Nav\Model\Data\Extractor\Nav\FieldDataExtractor;
use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;

class FieldDataExtractorMapping
{
    protected $mapping;
    protected $fieldDataExtractor;

    public function __construct(
        UnaryMapping $mapping,
        FieldDataExtractor $fieldDataExtractor
    ) {
        $this->mapping = $mapping;
        $this->fieldDataExtractor = $fieldDataExtractor;
    }

    public function apply(array $data)
    {
        return [
            $this->mapping->get() => $this->fieldDataExtractor->extract($data),
        ];
    }
}
