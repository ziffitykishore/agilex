<?php

namespace Creatuity\Nav\Model\Data\Transform\Nav;

class BooleanConditionDataTransform implements DataTransformInterface
{
    protected $condition;
    protected $valueSuccess;
    protected $valueFailure;

    public function __construct(callable $condition, $valueSuccess = true, $valueFailure = false)
    {
        $this->condition = $condition;
        $this->valueSuccess = $valueSuccess;
        $this->valueFailure = $valueFailure;
    }

    public function transform($data)
    {
        return (call_user_func($this->condition, $data)) ? $this->valueSuccess : $this->valueFailure;
    }
}
