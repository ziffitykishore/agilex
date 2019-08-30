<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PartySupplies\Catalog\Block\Product;

/**
 * Description of ListProduct.
 *
 * @author linux
 */
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    public function getSearchFilter()
    {
        $url = $this->getRequest()->getUri();
        $searchApplied = array_key_exists('q', $this->getRequest()->getParams());

        if ($searchApplied) {
            $text = $this->getRequest()->getParam('q');

            return '<span>'.$text." <a href='".$this->urlHelper->removeRequestParam($url, 'q', true)."'>x</a></span>";
        }
    }
}
