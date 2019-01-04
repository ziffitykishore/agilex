<?php

namespace SomethingDigital\ExtendedMiniCart\Plugin\Checkout\CustomerData;
 
class Cart {

    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, array $result)
    {
        $result['ethan_was_here'] = $result['subtotalAmount'] * 10 / 100;
        return $result;
    }
}
