<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

use Creatuity\Nav\Model\Data\Mapping\UnaryMapping;

class SingleFieldParameters implements ParametersInterface
{
    protected $mapping;
    protected $value;

    public function __construct(
        UnaryMapping $mapping,
        $value
    ) {
        $this->mapping = $mapping;
        $this->value = $value;
    }

    public function getParameters()
    {
        return [
            $this->mapping->get() => $this->value,
        ];
    }
}
