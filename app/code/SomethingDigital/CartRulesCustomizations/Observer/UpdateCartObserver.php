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
use Magento\Quote\Api\Data\CartItemInterfaceFactory;

class UpdateCartObserver implements ObserverInterface
{
    protected $freeGiftSku;
    protected $productRepository;
    protected $cart;
    protected $collector;
    protected $session;
    protected $logger;
    protected $quote;
    protected $quoteItemFactory;

    public function __construct(
        FreeGiftSku $freeGiftSku,
        ProductRepositoryInterface $productRepository,
        Cart $cart,
        TotalsCollector $collector,
        SessionManagerInterface $session,
        LoggerInterface $logger,
        Quote $quote,
        CartItemInterfaceFactory $quoteItemFactory
    ) {
        $this->freeGiftSku = $freeGiftSku;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->collector = $collector;
        $this->session = $session;
        $this->logger = $logger;
        $this->quote = $quote;
        $this->quoteItemFactory = $quoteItemFactory;
    }

    /**
     * We check if the gift ($0) already added to the cart.
     * We don't add next gift if is already added.
     * If the sku of the gift is already added to the cart and:
     * - qty == 1 : change price to $0
     * - qty > 1 : decrease qty (qty - 1) and add gift item
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $skusInCart = [];
        $skusInCartQty = [];
        $freeGiftIsInCart = false;
        foreach ($quote->getAllVisibleItems() as $item) {
            $skusInCart[] = $item->getSku();
            $skusInCartQty[$item->getSku()] = $item->getQty();
            $options = $item->getOptions();
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getCode() == 'free_gift' && $option->getValue() == 1) {
                        if (!in_array($item->getSku(), $this->freeGiftSku->skus)) {
                            $this->cart->removeItem($item->getId())->save();
                        } else {
                            $freeGiftIsInCart = true;
                        }
                    }
                }
            }
        }

        $addedGift = false;
        foreach ($this->freeGiftSku->skus as $giftSku) {
            if (!$this->session->getRemovedGifts()) {
                $removedGifts = [];
            } else {
                $removedGifts = $this->session->getRemovedGifts();
            }
            if (in_array($giftSku, $removedGifts) || $freeGiftIsInCart) {
                continue;
            }
            try {
                $product = $this->productRepository->get($giftSku);
                $quoteItem = $quote->addProduct($product, 1);
                if (!in_array($giftSku, $skusInCart) || $skusInCartQty[$giftSku] == 1) {
                    $quoteItem->setCustomPrice(0);
                    $quoteItem->setOriginalCustomPrice(0);
                    $quoteItem->addOption([
                        'product_id' => $product->getId(),
                        'product'    => $product,
                        'code' => 'free_gift',
                        'value' => true
                    ]);
                }
                if (in_array($giftSku, $skusInCart) && $skusInCartQty[$giftSku] > 1) {
                    $quoteItem->setQty($skusInCartQty[$giftSku] - 1);
                } else {
                    $quoteItem->setQty(1);
                }
                $quoteItem->save();
                $addedGift = true;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->logger->warning("Couldn't add free gift product $giftSku to the quote.");
            }
            if (in_array($giftSku, $skusInCart) && $skusInCartQty[$giftSku] > 1) {
                try {
                    $product = $this->productRepository->get($giftSku);
                    $quoteItem = $this->quoteItemFactory->create();
                    $quoteItem->setProduct($product);
                    $quoteItem->setCustomPrice(0);
                    $quoteItem->setOriginalCustomPrice(0);
                    $quoteItem->setQty(1);
                    $quoteItem->addOption([
                        'product_id' => $product->getId(),
                        'product'    => $product,
                        'code' => 'free_gift',
                        'value' => true
                    ]);
                    $quote->addItem($quoteItem);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->logger->warning("Couldn't add free gift product $giftSku to the quote.");
                }
            }
        }
        if ($addedGift) {
            $this->collector->collect($quote);
        }
    }
}