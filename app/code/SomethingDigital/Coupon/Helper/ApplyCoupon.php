<?php

namespace SomethingDigital\Coupon\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
 
class ApplyCoupon extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $cookieManager;
    private $session;
    private $checkoutSession;

    public function __construct(
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $session,
        Session $checkoutSession
    )
    {
        $this->cookieManager = $cookieManager;
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
    }

    public function apply()
    {
        $couponCode = $this->cookieManager->getCookie('coupon');
        if ($couponCode) {
            $coupon = $this->checkoutSession->getQuote()->setCouponCode($couponCode)
                ->collectTotals()
                ->save();

            $this->cookieManager->deleteCookie('coupon');

            if (!$coupon->getCouponCode()) {
                //if no coupon code is found set coupon code as suffix value
                $this->session->setSkuSuffix($couponCode);
            }
        }
    }
}
