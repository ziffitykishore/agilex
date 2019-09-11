<?php

namespace Creatuity\Nav\Model\Data\Transform\Nav;

class TitleCaseDataTransform implements DataTransformInterface
{
    public function transform($data)
    {
        return ucwords($data);
    }
}
