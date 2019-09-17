<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

class NullParameters implements ParametersInterface
{
    public function getParameters()
    {
        return [];
    }
}
