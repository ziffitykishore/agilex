<?php

namespace Earthlite\LayerNavigation\Plugin\Block\Swatches;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\UrlInterface;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered as SwatchesRenderLayered;
use Magento\Theme\Block\Html\Pager;
use Earthlite\LayerNavigation\Helper\Data;

class RenderLayered
{
    protected $_url;

    protected $_htmlPagerBlock;

    protected $_moduleHelper;

    protected $filter;

    public function __construct(
        UrlInterface $url,
        Pager $htmlPagerBlock,
        Data $moduleHelper
    ) {
        $this->_url            = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->_moduleHelper   = $moduleHelper;
    }

    public function beforeSetSwatchFilter(SwatchesRenderLayered $subject, AbstractFilter $filter)
    {
        $this->filter = $filter;

        return [$filter];
    }

    public function aroundBuildUrl(
        SwatchesRenderLayered $subject,
        $proceed,
        $attributeCode,
        $optionId
    ) {
        if (!$this->_moduleHelper->isEnabled()) {
            return $proceed($attributeCode, $optionId);
        }

        $attHelper = $this->_moduleHelper->getFilterModel();
        if ($attHelper->isMultiple($this->filter)) {
            $value = $attHelper->getFilterValue($this->filter);
            if (in_array($optionId, $value, true)) {
                $key = array_search($optionId, $value, true);
                if ($key !== false) {
                    unset($value[$key]);
                }
            } else {
                $value[] = $optionId;
            }
        } else {
            $value = [$optionId];
        }

        //Sort param on Url
        sort($value);

        $query = !empty($value) ? [$attributeCode => implode(',', $value)] : '';

        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}
