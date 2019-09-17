<?php

namespace Creatuity\Nav\Model\Service\Request;

use Creatuity\Nav\Model\Service\Request\Dimension\DimensionInterface;
use Creatuity\Nav\Model\Service\Request\Operation\OperationInterface;
use Creatuity\Nav\Model\Service\Request\Parameters\ParametersInterface;

class ServiceRequest
{
    protected $operation;
    protected $dimension;
    protected $parameters;

    public function __construct(
        OperationInterface $operation,
        DimensionInterface $dimension,
        ParametersInterface $parameters
    ) {
        $this->operation = $operation;
        $this->dimension = $dimension;
        $this->parameters = $parameters;
    }

    public function getMethod()
    {
        return "{$this->operation->getOperation()}{$this->dimension->getDimension()}";
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getDimension()
    {
        return $this->dimension;
    }

    public function getParameters()
    {
        return $this->parameters->getParameters();
    }
}
