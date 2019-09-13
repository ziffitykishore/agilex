<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters\Filter;

interface FilterInterface
{
    public function getField();

    public function getCriteria();
}
