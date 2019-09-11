<?php

namespace Creatuity\Nav\Model\Service\Request\Dimension;

class MultipleDimension implements DimensionInterface
{
    public function getDimension()
    {
        return 'Multiple';
    }

    public function isResultNested()
    {
        return true;
    }
}
