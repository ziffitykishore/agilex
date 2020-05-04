<?php

namespace Earthlite\LayerNavigation\Plugin\Model\Layer\Filter;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;
use Earthlite\LayerNavigation\Helper\Data as LayerHelper;


class Item
{
    protected $_url;

    protected $_htmlPagerBlock;

    protected $_request;

    protected $_moduleHelper;

    public function __construct(
        UrlInterface $url,
        Pager $htmlPagerBlock,
        RequestInterface $request,
        LayerHelper $moduleHelper
    ) {
        $this->_url            = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->_request        = $request;
        $this->_moduleHelper   = $moduleHelper;
    }

    public function aroundGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return $proceed();
        }

        $value       = [];
        $filter      = $item->getFilter();
        $filterModel = $this->_moduleHelper->getFilterModel();
        if ($filterModel->isSliderTypes($filter) || $filter->getData('range_mode')) {
            $value = ["from-to"];
        } elseif ($filterModel->isMultiple($filter)) {
            $requestVar = $filter->getRequestVar();
            if ($requestValue = $this->_request->getParam($requestVar)) {
                $value = explode(',', $requestValue);
            }
            if (!in_array($item->getValue(), $value, true)) {
                $value[] = $item->getValue();
            }
        }

        //Sort param on Url
        sort($value);

        if (!empty($value)) {
            $query = [
                $filter->getRequestVar()                 => implode(',', $value),
                // exclude current page from urls
                $this->_htmlPagerBlock->getPageVarName() => null,
            ];

            return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        }

        return $proceed();
    }

    public function aroundGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return $proceed();
        }

        $value       = [];
        $filter      = $item->getFilter();
        $filterModel = $this->_moduleHelper->getFilterModel();
        if ($filterModel->isMultiple($filter)) {
            $value = $filterModel->getFilterValue($filter);
            if (in_array((string) $item->getValue(), $value, true)) {
                $value = array_diff($value, [$item->getValue()]);
            }
        }

        $params['_query']       = [
            $filter->getRequestVar() => count($value) ? implode(',', $value) : $filter->getResetValue()
        ];
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_escape']      = true;

        return $this->_url->getUrl('*/*/*', $params);
    }
}
