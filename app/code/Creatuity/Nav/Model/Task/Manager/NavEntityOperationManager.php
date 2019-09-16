<?php

namespace Creatuity\Nav\Model\Task\Manager;

use Creatuity\Nav\Model\Service\Service;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Service\Request\Operation\OperationInterface;
use Creatuity\Nav\Model\Service\Request\Dimension\SingleDimension;
use Creatuity\Nav\Model\Service\Request\Parameters\ParametersFactory;

class NavEntityOperationManager
{
    protected $service;
    protected $operation;
    protected $parametersFactory;
    protected $fieldDataExtractorMappings;

    public function __construct(
        Service $service,
        OperationInterface $operation,
        ParametersFactory $parametersFactory,
        array $fieldDataExtractorMappings
    ) {
        $this->service = $service;
        $this->operation = $operation;
        $this->parametersFactory = $parametersFactory;
        $this->fieldDataExtractorMappings = $fieldDataExtractorMappings;
    }

    public function process(array $orderData)
    {
        $data = [];
        foreach ($this->fieldDataExtractorMappings as $fieldDataExtractorMapping) {
            $data = array_merge($data, $fieldDataExtractorMapping->apply($orderData));
        }

        $order = $this->service->process(
            new ServiceRequest(
                $this->operation,
                new SingleDimension(),
                $this->parametersFactory->create($data)
            )
        );

        return $order;
    }
}
