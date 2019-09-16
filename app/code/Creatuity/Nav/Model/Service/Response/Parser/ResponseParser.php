<?php

namespace Creatuity\Nav\Model\Service\Response\Parser;

use Creatuity\Nav\Model\Service\Object\ServiceObject;
use Creatuity\Nav\Model\Service\Request\Operation\OperationInterface;
use Creatuity\Nav\Model\Service\Request\Dimension\DimensionInterface;
use stdClass;

class ResponseParser
{
    protected $serviceObject;
    protected $operation;
    protected $dimension;

    public function __construct(
        ServiceObject $serviceObject,
        OperationInterface $operation,
        DimensionInterface $dimension
    ) {
        $this->serviceObject = $serviceObject;
        $this->operation = $operation;
        $this->dimension = $dimension;
    }

    public function parse(stdClass $data)
    {
        if ($this->operation->hasBooleanResult()
            || !$this->operation->hasResult()
        ) {
            return [];
        }

        $objectName = $this->serviceObject->getName();

        if ($this->dimension->isResultNested()) {
            $resultMember = "{$this->operation->getOperation()}{$this->dimension->getDimension()}_Result";

            if (!isset($data->$resultMember->$objectName)) {
                return [];
            }

            $objectData = $data->$resultMember->$objectName;

            if (!is_array($objectData)) {
                $objectData = [ $objectData ];
            }

            return $this->convertObjectArrayToArray($objectData);
        }

        return $this->convertObjectToArray($data->$objectName);
    }

    protected function convertObjectArrayToArray(array $objects)
    {
        $array = [];
        foreach ($objects as $object) {
            $array[] = $this->convertObjectToArray($object);
        }

        return $array;
    }

    protected function convertObjectToArray(stdClass $object)
    {
        return (array) $object;
    }
}
