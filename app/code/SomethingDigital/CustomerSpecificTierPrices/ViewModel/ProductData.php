<?php

namespace SomethingDigital\CustomerSpecificTierPrices\ViewModel;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Data;

class ProductData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    private $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }
    
    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->helper->getProduct();
    }

    public function getProductType()
    {
        /** @var \Magento\Catalog\Model\Product $currentProduct */
        $currentProduct = $this->getProduct();
        /** @var string $productType */
        $productType = $currentProduct->getTypeId();
 
        return $productType;
    }

}
