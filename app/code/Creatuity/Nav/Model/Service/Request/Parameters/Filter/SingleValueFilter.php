<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters\Filter;

class SingleValueFilter implements FilterInterface
{
    protected $field;
    protected $value;

    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getCriteria()
    {
        return $this->value;
    }
}
