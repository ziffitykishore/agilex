<?php

namespace SomethingDigital\SearchCustomization\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Session\SessionManagerInterface;


class Suffix implements ObserverInterface
{
    protected $quoteRepository;
    protected $cart;
    protected $session;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Cart $cart,
        SessionManagerInterface $session
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->cart = $cart;
        $this->session = $session;
    }
    
    public function execute(Observer $observer)
    {   
        $suffix = $this->session->getSkuSuffix();
        if ($suffix) {
            $currentQuote = $this->cart->getQuote();
            if ($currentQuote) {
                $quote = $this->quoteRepository->get($currentQuote->getId());
                $quote->setSuffix($suffix);
                $this->quoteRepository->save($quote);
            }
        }
    }
    
}
