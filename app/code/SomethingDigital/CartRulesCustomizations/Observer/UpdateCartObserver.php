<?php

namespace SomethingDigital\CartRulesCustomizations\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SomethingDigital\CartRulesCustomizations\Model\FreeGiftSku;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Framework\Session\SessionManagerInterface;
use Psr\Log\LoggerInterface;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;

class UpdateCartObserver implements ObserverInterface
{
    protected $freeGiftSku;
    protected $productRepository;
    protected $cart;
    protected $collector;
    protected $session;
    protected $logger;
    protected $quote;

    public function __construct(
        FreeGiftSku $freeGiftSku,
        ProductRepositoryInterface $productRepository,
        Cart $cart,
        TotalsCollector $collector,
        SessionManagerInterface $session,
        LoggerInterface $logger,
        Quote $quote
    ) {
        $this->freeGiftSku = $freeGiftSku;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->collector = $collector;
        $this->session = $session;
        $this->logger = $logger;
        $this->quote = $quote;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $skusInCart = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $skusInCart[] = $item->getSku();
            $options = $item->getOptions();
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getCode() == 'free_gift' && $option->getValue() == 1) {
                        if (!in_array($item->getSku(), $this->freeGiftSku->skus)) {
                            $this->cart->removeItem($item->getId())->save();
                        }
                    }
                }
            }
        }
        $addedGift = false;
        foreach ($this->freeGiftSku->skus as $giftSku) {
            if (!in_array($giftSku, $skusInCart) && !in_array($giftSku, $this->session->getRemovedGifts())) {
                try {
                    $product = $this->productRepository->get($giftSku);
                    $quoteItem = $quote->addProduct($product, 1);
                    $quoteItem->setCustomPrice(0);
                    $quoteItem->setOriginalCustomPrice(0);
                    $quoteItem->addOption([
                        'product_id' => $product->getId(),
                        'product'    => $product,
                        'code' => 'free_gift',
                        'value' => true
                    ]);
                    $quoteItem->save();
                    $addedGift = true;
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->logger->warning("Couldn't add free gift product $giftSku to the quote.");
                }
            }
        }
        if ($addedGift) {
            $this->collector->collect($quote);
        }

        $this->quote->repriceCustomerQuote();
    }
}