<?php

namespace Creatuity\Nav\Model\Service\Request\Parameters\Filter;

class FilterGroup
{
    protected $filters = [];

    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    public function addFilter(FilterInterface $requestFilter)
    {
        $this->filters[] = [
            'Field'    => $requestFilter->getField(),
            'Criteria' => $requestFilter->getCriteria(),
        ];
    }

    public function getFilters()
    {
        return $this->filters;
    }
}
