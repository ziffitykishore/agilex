<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use Magento\Quote\Model\Quote\Item\Processor;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Framework\DataObject;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use SomethingDigital\CustomerSpecificPricing\Helper\Data as ProductHelper;
use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class QuoteItemProcessor
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepo;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * @var SpotPricingApi
     */
    private $spotPricingApi;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var PriceCurrencyInterface
     */
    protected $currency;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct (
        CustomerRepositoryInterface $customerRepo,
        ProductRepositoryInterface $productRepo,
        Session $session,
        LoggerInterface $logger, 
        ProductHelper $productHelper,
        SpotPricingApi $spotPricingApi,
        ArrayManager $arrayManager,
        Cart $cart,
        PriceCurrencyInterface $currency,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRepo = $customerRepo;
        $this->productRepo = $productRepo;
        $this->session = $session;
        $this->logger = $logger;
        $this->productHelper = $productHelper;
        $this->spotPricingApi = $spotPricingApi;
        $this->arrayManager = $arrayManager;
        $this->cart = $cart;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
    }

    public function beforePrepare(
        Processor $subject,
        Item $item, 
        DataObject $request, 
        Product $candidate
    ) {
        try {
            $id = $candidate->getId();
            /** @var ProductInterface $product */
            $product = $this->productRepo->getById($id);

            $sku = $product->getSku();
            /** @var int $qty */
            $qty = $candidate->getCartQty();

            $items = $this->cart->getQuote()->getAllVisibleItems();

            $totalItemQty = (int)$qty;
            foreach ( $items as $quoteItem) {
                if ($quoteItem->getProductId() == $id) {
                    $totalItemQty += $quoteItem->getQty();
                }
            }

            $prices = $this->spotPricingApi->getSpotPrice([$sku]);

            if (!$prices) {
                return [$item, $request, $candidate];
            }

            $store = $this->storeManager->getStore()->getStoreId();

            foreach ($prices as $key => $productPrices) {
                $specialPrice = $this->arrayManager->get('DiscountPrice', $productPrices);
                $regularProductPrice = $this->currency->convert($product->getPrice(), $store);
                if ($specialPrice && $specialPrice < $regularProductPrice) {
                    $request->setCustomPrice($specialPrice);
                    $item->setIsCustomerSpecificPriceApplied(true);
                }
                $tierPrice = $this->productHelper->getTierPrice($prices, $sku, $totalItemQty);
                if ($tierPrice) {
                    $request->setCustomPrice($tierPrice);
                    $item->setIsCustomerSpecificTierPriceApplied(true);
                }
            }

        } catch (LocalizedException $e) {
            $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
        }
        return [$item, $request, $candidate];
    }
}