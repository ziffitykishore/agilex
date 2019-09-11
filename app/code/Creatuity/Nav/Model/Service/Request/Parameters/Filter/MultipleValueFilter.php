<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters\Filter;

class MultipleValueFilter implements FilterInterface
{
    protected $field;
    protected $values = [];

    public function __construct($field, array $values)
    {
        $this->field = $field;
        $this->values = $values;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getCriteria()
    {
        return str_replace(' ', '', implode('|', $this->values));
    }
}
