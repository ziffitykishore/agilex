<?php

namespace SomethingDigital\Coupon\Plugin;

use SomethingDigital\Coupon\Helper\ApplyCoupon;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart as CartModel;

class Cart
{
    protected $applyCoupon;
    protected $cart;
    protected $quoteRepository;
    
    public function __construct(
        ApplyCoupon $applyCoupon,
        CartModel $cart,
        CartRepositoryInterface $quoteRepository
    )
    {
        $this->applyCoupon = $applyCoupon;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeExecute(\Magento\Checkout\Controller\Cart\Index $subject)
    {
        $this->applyCoupon->apply();

        $currentQuote = $this->cart->getQuote();
        if ($currentQuote && $currentQuote->getId()) {
            $quote = $this->quoteRepository->get($currentQuote->getId());
            $this->quoteRepository->save($quote);
        }
    }
}
