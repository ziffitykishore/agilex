<?php

namespace Creatuity\Nav\Model\Data\Mapping;

class UnaryMapping
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function get()
    {
        return $this->key;
    }
}
