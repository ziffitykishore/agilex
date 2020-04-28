<?php

namespace Earthlite\LayerNavigation\Helper;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Earthlite\LayerNavigation\Model\Layer\Filter;

/**
 * 
 * @package Earthlite\LayerNavigation\Helper
 */
class Data extends \Mageplaza\AjaxLayer\Helper\Data
{
    const FILTER_TYPE_SLIDER = 'slider';
    const FILTER_TYPE_LIST   = 'list';
   
    protected $filterModel;

    /**
     * @param $filters
     *
     * @return mixed
     */
    public function getLayerConfiguration($filters)
    {
        $filterParams = $this->_getRequest()->getParams();
        foreach ($filterParams as $key => $param) {
            $filterParams[$key] = htmlspecialchars($param);
        }

        $config = new DataObject([
            'active'             => array_keys($filterParams),
            'params'             => $filterParams,            
            'isAjax'             => $this->ajaxEnabled()
        ]);

        $this->getFilterModel()->getLayerConfiguration($filters, $config);

        return self::jsonEncode($config->getData());
    }

    /**
     * @return Filter
     */
    public function getFilterModel()
    {
        if (!$this->filterModel) {
            $this->filterModel = $this->objectManager->create(Filter::class);
        }

        return $this->filterModel;
    }    
}
