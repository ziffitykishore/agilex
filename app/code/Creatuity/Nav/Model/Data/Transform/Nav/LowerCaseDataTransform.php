<?php

namespace Creatuity\Nav\Model\Data\Transform\Nav;

class LowerCaseDataTransform implements DataTransformInterface
{
    public function transform($data)
    {
        return strtolower($data);
    }
}
