<?php

namespace Creatuity\Nav\Model\Service\Request\Operation;

class CreateOperation implements OperationInterface
{
    public function getOperation()
    {
        return 'Create';
    }

    public function hasResult()
    {
        return true;
    }

    public function hasBooleanResult()
    {
        return false;
    }
}
