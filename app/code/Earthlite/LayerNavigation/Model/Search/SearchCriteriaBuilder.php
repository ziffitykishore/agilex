<?php

namespace Earthlite\LayerNavigation\Model\Search;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder as SourceSearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Earthlite\LayerNavigation\Helper\Data as LayerHelper;


class SearchCriteriaBuilder extends SourceSearchCriteriaBuilder
{
    protected $helper;

    public function __construct(
        LayerHelper $helper,
        ObjectFactory $objectFactory,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->helper = $helper;

        parent::__construct($objectFactory, $filterGroupBuilder, $sortOrderBuilder);
    }

    public function removeFilter($attributeCode)
    {
        $this->filterGroupBuilder->removeFilter($attributeCode);

        return $this;
    }

    public function cloneObject()
    {
        $cloneObject = clone $this;
        $cloneObject->setFilterGroupBuilder($this->filterGroupBuilder->cloneObject());

        return $cloneObject;
    }

    public function setFilterGroupBuilder($filterGroupBuilder)
    {
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    protected function _getDataObjectType()
    {
        return SearchCriteria::class;
    }

    public function create()
    {
        if ($this->helper->versionCompare('2.2.0')) {
            $this->data[SearchCriteria::FILTER_GROUPS] = [$this->filterGroupBuilder->create()];
            $this->data[SearchCriteria::SORT_ORDERS]   = [$this->sortOrderBuilder->create()];
        }

        return parent::create();
    }

    public function addFilter(Filter $filter)
    {
        $this->filterGroupBuilder->addFilter($filter);

        return $this;
    }
}
