<?php

namespace SomethingDigital\Coupon\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\SalesRule\Model\Coupon;
 
class ApplyCoupon extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $cookieManager;
    private $session;
    private $checkoutSession;
    protected $coupon;

    public function __construct(
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $session,
        Session $checkoutSession,
        Coupon $coupon
    )
    {
        $this->cookieManager = $cookieManager;
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
        $this->coupon = $coupon;
    }

    public function apply()
    {
        $couponCode = $this->cookieManager->getCookie('coupon');
        if ($couponCode) {
            $coupon = $this->checkoutSession->getQuote()->setCouponCode($couponCode)
                ->collectTotals()
                ->save();

            $this->cookieManager->deleteCookie('coupon');
            setcookie('coupon', '', time() - 3600, '/');

            $ruleId = $this->coupon->loadByCode($couponCode)->getRuleId();

            if (empty($ruleId)) {
                //if no coupon code is found set coupon code as suffix value
                $couponCodeHasSymbols = strcspn($couponCode, '~!@#$%^&*()=+-_?:<>[]{}') !== strlen($couponCode);
                if (!$couponCodeHasSymbols) {
                    $this->session->setSkuSuffix($couponCode);
                }
            }
        }
    }
}
