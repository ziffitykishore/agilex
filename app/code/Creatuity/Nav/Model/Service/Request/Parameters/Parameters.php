<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

class Parameters implements ParametersInterface
{
    protected $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
