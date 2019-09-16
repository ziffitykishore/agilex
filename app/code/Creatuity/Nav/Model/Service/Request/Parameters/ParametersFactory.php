<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

class ParametersFactory
{
    public function create(array $parameters = [])
    {
        return new Parameters($parameters);
    }
}
