<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Stdlib\ArrayManager;
use SomethingDigital\CustomerSpecificPricing\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;

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

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    private $helper;


    public function __construct(
        SpotPricingApi $spotPricingApi,
        LoggerInterface $logger,
        Cart $cart,
        ArrayManager $arrayManager,
        Data $helper,
        ProductRepositoryInterface $productRepo
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->logger = $logger;
        $this->cart = $cart;
        $this->arrayManager = $arrayManager;
        $this->helper = $helper;
        $this->productRepo = $productRepo;
    }

    public function repriceCustomerQuote()
    {
        $items = $this->cart->getQuote()->getAllItems();

        if ($items) {
            foreach ($items as $item) {
                $product = $this->productRepo->getById($item->getProductId());
                $price = $product->getPrice();

                try {
                    if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                        $prices = $this->spotPricingApi->getSpotPrice($item->getSku());
                        $spotPrice = $this->arrayManager->get('body/DiscountPrice', $prices);
                        $customPrice = null;
                        if ($spotPrice && $spotPrice < $price) {
                            $customPrice = $spotPrice;
                        }
                        $tierPrice = $this->helper->getTierPrice($prices, $item->getQty());
                        if ($tierPrice) {
                            $customPrice = $tierPrice;
                        }
                        if ($customPrice) {
                            $item->setCustomPrice($customPrice);
                            $item->setOriginalCustomPrice($customPrice);
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
