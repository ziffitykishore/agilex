<?php

namespace SomethingDigital\CartRulesCustomizations\Plugin;

use Magento\Checkout\Controller\Cart\CouponPost;
use Magento\Framework\Session\SessionManagerInterface;

class ClearRemovedGifts
{
    protected $session;
    
    public function __construct(
        SessionManagerInterface $session
    ) {
        $this->session = $session;
    }

    public function beforeExecute(CouponPost $subject)
    {
        $this->session->setRemovedGifts([]);
    }
}