<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Stdlib\ArrayManager;
use SomethingDigital\CustomerSpecificPricing\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;

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

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;


    public function __construct(
        SpotPricingApi $spotPricingApi,
        LoggerInterface $logger,
        Cart $cart,
        ArrayManager $arrayManager,
        Data $helper,
        ProductRepositoryInterface $productRepo,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->logger = $logger;
        $this->cart = $cart;
        $this->arrayManager = $arrayManager;
        $this->helper = $helper;
        $this->productRepo = $productRepo;
        $this->quoteRepository = $quoteRepository;
    }

    public function repriceCustomerQuote($suffix = null)
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

                $allPrices = $this->spotPricingApi->getSpotPrice($productSkus, $suffix);
                if (!$allPrices) {
                    return;
                }
                if (empty($allPrices) || !is_array($allPrices)) {
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
                            $item->setIsCustomerSpecificTierPriceApplied(true);
                        }
                        $item->setCustomPrice($customPrice);
                        $item->setOriginalCustomPrice($customPrice);
                        $item->getProduct()->setIsSuperMode(true);

                        $item->setIsCustomerSpecificPriceApplied(true);
                        $item->save();
                    }
                }

                $quote = $this->quoteRepository->get($this->cart->getQuote()->getId());
                $quote->collectTotals();
                $this->quoteRepository->save($quote);

            } catch (LocalizedException $e) {
                $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
            }
        }
    }
}
