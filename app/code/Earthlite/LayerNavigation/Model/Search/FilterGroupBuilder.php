<?php

namespace Earthlite\LayerNavigation\Model\Search;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder as SourceFilterGroupBuilder;
use Magento\Framework\App\RequestInterface;


class FilterGroupBuilder extends SourceFilterGroupBuilder
{    
    protected $_request;


    public function __construct(
        ObjectFactory $objectFactory,
        FilterBuilder $filterBuilder,
        RequestInterface $request
    ) {
        $this->_request = $request;

        parent::__construct($objectFactory, $filterBuilder);
    }

    public function cloneObject()
    {
        $cloneObject = clone $this;
        $cloneObject->setFilterBuilder(clone $this->_filterBuilder);

        return $cloneObject;
    }

    public function setFilterBuilder($filterBuilder)
    {
        $this->_filterBuilder = $filterBuilder;
    }

    public function removeFilter($attributeCode)
    {
        if (isset($this->data[FilterGroup::FILTERS]) && is_array($this->data[FilterGroup::FILTERS])) {
            foreach ($this->data[FilterGroup::FILTERS] as $key => $filter) {
                if ($filter->getField() === $attributeCode) {
                    if ($attributeCode === 'category_ids'
                        && ($filter->getValue() === $this->_request->getParam('id'))
                    ) {
                        continue;
                    }
                    unset($this->data[FilterGroup::FILTERS][$key]);
                }
            }
        }

        return $this;
    }

    protected function _getDataObjectType()
    {
        return FilterGroup::class;
    }
}
