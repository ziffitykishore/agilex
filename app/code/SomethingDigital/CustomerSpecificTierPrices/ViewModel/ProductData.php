<?php

namespace SomethingDigital\CustomerSpecificTierPrices\ViewModel;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Registry;

class ProductData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
   /**
    * @var Registry
    */
    private $coreRegistry;

    public function __construct(
        Registry $registry
    ) {
        $this->coreRegistry = $registry;
        
    }
    
    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
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
