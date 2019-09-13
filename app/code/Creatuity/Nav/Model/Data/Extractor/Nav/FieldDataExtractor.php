<?php

namespace Creatuity\Nav\Model\Data\Extractor\Nav;

use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;

class FieldDataExtractor
{
    protected $mapping;

    public function __construct(
        UnaryMapping $mapping
    ) {
        $this->mapping = $mapping;
    }

    public function extract(array $data)
    {
        if (!isset($data[$this->mapping->get()])) {
            throw new \Exception("Field {$this->mapping->get()} is NOT defined");
        }

        return $data[$this->mapping->get()];
    }
}
