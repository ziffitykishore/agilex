<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

use Creatuity\Nav\Model\Service\Object\ServiceObject;

class EntityParameters implements ParametersInterface
{
    protected $serviceObject;
    protected $parameters;

    public function __construct(
        ServiceObject $serviceObject,
        array $parameters
    ) {
        $this->parameters = $parameters;
        $this->serviceObject = $serviceObject;
    }

    public function getParameters()
    {
        return [
            $this->serviceObject->getName() => $this->parameters,
        ];
    }
}
