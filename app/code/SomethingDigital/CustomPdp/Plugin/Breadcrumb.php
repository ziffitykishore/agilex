<?php

namespace SomethingDigital\CustomPdp\Plugin;

use Magento\Catalog\Helper\Data;

class Breadcrumb
{
    public function __construct(
        Data $catalogData
    ) {
        $this->catalogData = $catalogData;
    }

    /**
     * Replace product name with SKU
     *
     * @param \Magento\Catalog\ViewModel\Product\Breadcrumbs $subject
     * @param type $result
     */
    public function afterGetProductName(\Magento\Catalog\ViewModel\Product\Breadcrumbs $subject, $result)
    {
        return $this->catalogData->getProduct() !== null
            ? '#' . $this->catalogData->getProduct()->getSku()
            : '';
    }
}
