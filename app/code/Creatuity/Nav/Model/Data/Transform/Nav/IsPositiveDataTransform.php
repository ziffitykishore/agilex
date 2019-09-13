<?php

namespace Creatuity\Nav\Model\Data\Transform\Nav;

class IsPositiveDataTransform extends BooleanConditionDataTransform
{
    public function __construct()
    {
        parent::__construct(
            function($data) {
                return $data > 0;
            }
        );
    }
}
