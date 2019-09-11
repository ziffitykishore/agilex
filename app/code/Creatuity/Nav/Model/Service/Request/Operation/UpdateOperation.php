<?php

namespace Creatuity\Nav\Model\Service\Request\Operation;

class UpdateOperation implements OperationInterface
{
    public function getOperation()
    {
        return 'Update';
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
