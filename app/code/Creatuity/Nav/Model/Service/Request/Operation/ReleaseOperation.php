<?php

namespace Creatuity\Nav\Model\Service\Request\Operation;

class ReleaseOperation implements OperationInterface
{
    public function getOperation()
    {
        return 'Release';
    }

    public function hasResult()
    {
        return false;
    }

    public function hasBooleanResult()
    {
        return false;
    }
}
