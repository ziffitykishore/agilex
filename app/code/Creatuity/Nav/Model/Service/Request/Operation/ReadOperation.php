<?php

namespace Creatuity\Nav\Model\Service\Request\Operation;

class ReadOperation implements OperationInterface
{
    public function getOperation()
    {
        return 'Read';
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
