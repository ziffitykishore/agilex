<?php

/**
 * Catalog layer filter renderer
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Prashant\Priceslider\Block;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer {

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter) {
        $this->assign('filterItems', $filter->getItems());
        $this->assign('filter', $filter);
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }

    public function getPriceRange($filter) {
        $filterprice = array('min' => 0, 'max' => 0);
        $priceArr = $filter->getResource()->loadPrices(10000000000);
        $filterprice['min'] = reset($priceArr);
        $filterprice['max'] = end($priceArr);
        return $filterprice;
    }

    public function getFilterUrl($filter) {
        $query = ['price' => ''];
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

}
