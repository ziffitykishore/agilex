<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

use Creatuity\Nav\Model\Service\Object\ServiceObject;

class EntityParametersFactory
{
    protected $serviceObject;

    public function __construct(ServiceObject $serviceObject)
    {
        $this->serviceObject = $serviceObject;
    }

    public function create(array $parameters = [])
    {
        return new EntityParameters($this->serviceObject, $parameters);
    }
}
