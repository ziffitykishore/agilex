<?php

namespace Creatuity\Nav\Model\Factory;

class GenericFactory
{
    protected $objects;

    public function __construct(array $objects)
    {
        $this->objects = $objects;
    }

    public function get($type)
    {
        if (!isset($this->objects[$type])) {
            throw new \Exception("No object with Type '{$type}' exists in object list");
        }

        return $this->objects[$type];
    }
}
