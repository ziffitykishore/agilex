<?php

namespace SomethingDigital\CartRulesCustomizations\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SomethingDigital\CartRulesCustomizations\Model\FreeGiftSku;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Model\Quote\TotalsCollector;


class UpdateCartObserver implements ObserverInterface
{
    protected $freeGiftSku;
    protected $productRepository;

    public function __construct(
        FreeGiftSku $freeGiftSku,
        ProductRepositoryInterface $productRepository,
        TotalsCollector $collector
    ) {
        $this->freeGiftSku = $freeGiftSku;
        $this->productRepository = $productRepository;
        $this->collector = $collector;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $skusInCart = [];
        $addedGift = false;
        foreach ($this->freeGiftSku->skus as $giftSku) {
            if (!in_array($giftSku, $skusInCart)) {
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
                    
                }
            }
        }
        if ($addedGift) {
            $this->collector->collect($quote);
        }
    }
}