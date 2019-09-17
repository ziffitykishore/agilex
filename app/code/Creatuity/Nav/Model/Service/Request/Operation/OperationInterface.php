<?php

namespace Creatuity\Nav\Model\Service\Request\Operation;

interface OperationInterface
{
    public function getOperation();

    public function hasResult();

    public function hasBooleanResult();
}
