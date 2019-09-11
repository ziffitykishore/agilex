<?php

namespace Creatuity\Nav\Model\Data\Transform\Nav;

use Creatuity\Nav\Exception\InvalidTypeCastException;

class TypeCastDataTransform implements DataTransformInterface
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function transform($data)
    {
        if (!@settype($data, $this->type)) {
            throw new InvalidTypeCastException("Failed to convert Data '{$data}' to Type '{$this->type}'");
        }

        return $data;
    }
}
