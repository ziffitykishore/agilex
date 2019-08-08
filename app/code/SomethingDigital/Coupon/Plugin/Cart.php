<?php

namespace SomethingDigital\Coupon\Plugin;

use SomethingDigital\Coupon\Helper\ApplyCoupon;

class Cart
{
    protected $applyCoupon;
    
    public function __construct(
        ApplyCoupon $applyCoupon
    )
    {
        $this->applyCoupon = $applyCoupon;
    }

    public function beforeExecute(\Magento\Checkout\Controller\Cart\Index $subject)
    {
        $this->applyCoupon->apply();
    }
}
