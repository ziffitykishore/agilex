<?php
namespace PartySupplies\Catalog\Plugin;

class Quote
{
    public function beforeAddProduct(
        $subject,
        \Magento\Catalog\Model\Product $product
    )   {
        
        if ($product->getTypeId() == 'simple' || $product->getTypeId() == 'bundle') {

            if($product->getFinalPrice() == 0){
                throw new \Magento\Framework\Exception\LocalizedException(
                   __('Cannot add product to cart.')
               );
            }

        } elseif ($product->getTypeId() == 'grouped') {
           $childProducts = $product->getTypeInstance()->getAssociatedProducts($product);

           foreach ($childProducts as $child) {
                if($child->getFinalPrice() == 0) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                       __('Cannot add product to cart.')
                   );
                }
           }

        } elseif ($product->getTypeId() == 'configurable') {
            $childProducts = $product->getTypeInstance()->getUsedProducts($product);

           foreach ($childProducts as $child) {
                if($child->getFinalPrice() == 0) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                       __('Cannot add product to cart.')
                   );
                }
           }
        }
    }
}