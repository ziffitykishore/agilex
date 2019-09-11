<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters;

use Creatuity\Nav\Model\Service\Request\Parameters\Filter\FilterGroup;

class FilterParameters implements ParametersInterface
{
    protected $filterGroup;

    public function __construct(
        FilterGroup $filterGroup
    ) {
        $this->filterGroup = $filterGroup;
    }

    public function getParameters()
    {
        return [
            'filter'  => $this->filterGroup->getFilters(),
            'setSize' => 0,
        ];
    }
}
