<?php

namespace SomethingDigital\Coupon\Plugin;

use SomethingDigital\Coupon\Helper\ApplyCoupon;

protected $applyCoupon;

class Cart
{
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
