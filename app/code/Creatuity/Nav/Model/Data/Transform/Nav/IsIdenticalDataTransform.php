<?php

namespace Creatuity\Nav\Model\Data\Transform\Nav;

class IsIdenticalDataTransform extends BooleanConditionDataTransform
{
    public function __construct($value)
    {
        parent::__construct(
            function($data) use ($value) {
                return $data === $value;
            }
        );
    }
}
