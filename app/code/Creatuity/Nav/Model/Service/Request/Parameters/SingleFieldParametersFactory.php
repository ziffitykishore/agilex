<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;

class SingleFieldParametersFactory
{
    protected $mapping;

    public function __construct(UnaryMapping $mapping)
    {
        $this->mapping = $mapping;
    }

    public function create($value)
    {
        return new SingleFieldParameters($this->mapping, $value);
    }
}
