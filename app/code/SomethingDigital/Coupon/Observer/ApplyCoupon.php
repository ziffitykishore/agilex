<?php 

namespace SomethingDigital\Coupon\Observer;

use Magento\Framework\Event\ObserverInterface;
use SomethingDigital\Coupon\Helper\ApplyCoupon as ApplyCouponHelper;

class ApplyCoupon implements ObserverInterface
{
    protected $applyCoupon;
    
    public function __construct(
        ApplyCouponHelper $applyCoupon
    ) {
        $this->applyCoupon = $applyCoupon;
    }

   public function execute(\Magento\Framework\Event\Observer $observer)
   {
       $this->applyCoupon->apply();
   }
}
