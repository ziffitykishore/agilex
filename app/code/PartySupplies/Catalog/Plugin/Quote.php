<?php
namespace PartySupplies\Catalog\Plugin;

class Quote
{
    public function beforeAddProduct(
        $subject,
        \Magento\Catalog\Model\Product $product
    )   {
        
        if($product->getFinalPrice() == 0){
            throw new \Magento\Framework\Exception\LocalizedException(
               __('Cannot add product to cart.')
           );
        }
    }
}