<?php

namespace SomethingDigital\SearchCustomization\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Session\SessionManagerInterface;

class AddSuffixToQuote
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

    public function afterSaveAffectedProducts(\Magento\AdvancedCheckout\Model\Cart $subject, $cart = null, $saveQuote = true)
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
        return $subject;
    }
}
