<?php

namespace Creatuity\Nav\Model\Service\Request\Operation;

class DeleteOperation implements OperationInterface
{
    public function getOperation()
    {
        return 'Delete';
    }

    public function hasResult()
    {
        return true;
    }

    public function hasBooleanResult()
    {
        return true;
    }
}
