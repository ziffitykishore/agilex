<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Stdlib\ArrayManager;

class Quote
{
    /**
     * @var SpotPricingApi
     */
    private $spotPricingApi;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var ArrayManager
     */
    private $arrayManager;


    public function __construct(
        SpotPricingApi $spotPricingApi,
        LoggerInterface $logger,
        Cart $cart,
        ArrayManager $arrayManager
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->logger = $logger;
        $this->cart = $cart;
        $this->arrayManager = $arrayManager;
    }

    public function repriceCustomerQuote()
    {
        $items = $this->cart->getQuote()->getAllItems();

        if ($items) {
            foreach ($items as $item) {
                $price = $item->getPrice();
                try {
                    if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                        $prices = $this->spotPricingApi->getSpotPrice($item->getSku());
                        $spotPrice = $this->arrayManager->get('body/Price', $prices);
                        if ($spotPrice && $spotPrice < $price) {
                            $item->setCustomPrice($spotPrice);
                            $item->setOriginalCustomPrice($spotPrice);
                            $item->getProduct()->setIsSuperMode(true);
                            $item->save(); 
                        }
                    }
                } catch (LocalizedException $e) {
                    $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
                }
            }
        }
    }
}
