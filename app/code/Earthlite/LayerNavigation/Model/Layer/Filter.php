<?php

namespace Earthlite\LayerNavigation\Model\Layer;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Earthlite\LayerNavigation\Helper\Data as LayerHelper;


class Filter
{

    protected $request;

    protected $sliderTypes = [LayerHelper::FILTER_TYPE_SLIDER];

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function getLayerConfiguration($filters, $config)
    {
        $slider = [];
        foreach ($filters as $filter) {
            if ($this->isSliderTypes($filter) && $filter->getItemsCount()) {
                $slider[$filter->getRequestVar()] = $filter->getSliderConfig();
            }
        }
        $config->setData('slider', $slider);

        return $this;
    }

    public function isSliderTypes($filter, $types = null)
    {
        $filterType = $this->getFilterType($filter);
        $types      = $types ?: $this->sliderTypes;

        return in_array($filterType, $types, true);
    }

    public function getFilterType($filter, $compareType = null)
    {
        $type = LayerHelper::FILTER_TYPE_LIST;
        if ($filter->getRequestVar() === 'price') {
            $type = LayerHelper::FILTER_TYPE_SLIDER;
        }

        return $compareType ? ($type === $compareType) : $type;
    }

    public function getItemUrl($item)
    {
        if ($this->isSelected($item)) {
            return $item->getRemoveUrl();
        }

        return $item->getUrl();
    }

    public function isSelected(Item $item)
    {
        $filterValue = $this->getFilterValue($item->getFilter());

        return !empty($filterValue) && in_array((string) $item->getValue(), $filterValue, true);
    }

    public function getFilterValue($filter, $explode = true)
    {
        $filterValue = $this->request->getParam($filter->getRequestVar());
        if (empty($filterValue)) {
            return [];
        }

        return $explode ? explode(',', $filterValue) : [$filterValue];
    }

    public function isShowCounter($filter)
    {
        return false;
    }

    public function isMultiple($filter)
    {
        return !($this->isSliderTypes($filter) || $filter->getRequestVar() === 'price');
    }

    public function isOptionReducesResults($filter, $optionCount, $totalSize)
    {
        $result = $optionCount <= $totalSize;

        if ($this->isShowZero($filter)) {
            return $result;
        }

        return $optionCount && $result;
    }

    public function isShowZero($filter)
    {
        return false;
    }
}
