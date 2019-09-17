<?php

namespace Creatuity\Nav\Model\Map\Filter;

class AttributeFilter
{
    protected $attribute;
    protected $condition;

    public function __construct($attribute, $condition)
    {
        $this->attribute = $attribute;
        $this->condition = $condition;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function getCondition()
    {
        return $this->condition;
    }
}
