<?php

namespace Creatuity\Nav\Model\Service\Request\Dimension;

class SingleDimension implements DimensionInterface
{
    public function getDimension()
    {
        return '';
    }

    public function isResultNested()
    {
        return false;
    }
}
