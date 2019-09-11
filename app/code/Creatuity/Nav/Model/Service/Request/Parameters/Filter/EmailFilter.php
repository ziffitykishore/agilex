<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters\Filter;

class EmailFilter extends SingleValueFilter
{
    public function getCriteria()
    {
        return '@'.str_replace('@', '?', $this->value);
    }
}
