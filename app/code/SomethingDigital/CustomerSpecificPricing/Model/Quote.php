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

    public function repriceCustomerQuote($saveQuoteItem = false, $suffix = null)
    {
        $items = $this->cart->getQuote()->getAllItems();

        if ($items) {
            $productRegularPrices = [];
            $productSkus = [];

            try {
                foreach ($items as $item) {
                    $product = $this->productRepo->getById($item->getProductId());
                    $productsRegularPrices[$product->getSku()] = $product->getPrice();
                    $productSkus[] = $product->getSku();
                }

                $pricesResponse = $this->spotPricingApi->getSpotPrice($productSkus, $suffix);
                $allPrices = $this->arrayManager->get('body', $pricesResponse);
                if (empty($allPrices)) {
                    return;
                }
                $spotPrices = [];
                foreach ($allPrices as $productPrices) {
                    $sku = $this->arrayManager->get('Sku', $productPrices);
                    $spotPrice = $this->arrayManager->get('DiscountPrice', $productPrices);
                    $spotPrices[$sku] = $spotPrice;
                }

                foreach ($items as $item) {
                    if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {

                        $isFreeGift = false;
                        $itemOptions = $item->getOptions();
                        if ($itemOptions) {
                            foreach ($itemOptions as $option) {
                                if ($option->getCode() == 'free_gift') {
                                    $isFreeGift = true;
                                }
                            }
                        }
                        if ($isFreeGift) {
                            continue;
                        }
                        $customPrice = null;
                        $spotPrice = isset($spotPrices[$item->getSku()]) ? $spotPrices[$item->getSku()] : false;
                        if ($spotPrice && $spotPrice < $productsRegularPrices[$item->getSku()]) {
                            $customPrice = $spotPrice;
                        }
                        $tierPrice = $this->helper->getTierPrice($allPrices, $item->getSku(), $item->getQty());
                        if ($tierPrice) {
                            $customPrice = $tierPrice;
                        }
                        $item->setCustomPrice($customPrice);
                        $item->setOriginalCustomPrice($customPrice);
                        $item->getProduct()->setIsSuperMode(true);
                        if ($saveQuoteItem) {
                            $item->save();
                        }
                    }
                }

            } catch (LocalizedException $e) {
                $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
            }
        }
    }
}
