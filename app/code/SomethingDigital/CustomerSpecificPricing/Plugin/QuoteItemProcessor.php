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

    public function __construct (
        CustomerRepositoryInterface $customerRepo,
        ProductRepositoryInterface $productRepo,
        Session $session,
        LoggerInterface $logger, 
        ProductHelper $productHelper,
        SpotPricingApi $spotPricingApi,
        ArrayManager $arrayManager,
        Cart $cart
    ) {
        $this->customerRepo = $customerRepo;
        $this->productRepo = $productRepo;
        $this->session = $session;
        $this->logger = $logger;
        $this->productHelper = $productHelper;
        $this->spotPricingApi = $spotPricingApi;
        $this->arrayManager = $arrayManager;
        $this->cart = $cart;
    }

    public function beforePrepare(
        Processor $subject,
        Item $item, 
        DataObject $request, 
        Product $candidate
    ) {
        if ($this->session->isLoggedIn()) {
            try {
                $id = $candidate->getId();
                /** @var ProductInterface $product */
                $product = $this->productRepo->getById($id);

                $sku = $product->getSku();
                /** @var int $qty */
                $qty = $candidate->getCartQty();

                $items = $this->cart->getQuote()->getAllVisibleItems();

                $totalItemQty = $qty;
                foreach ( $items as $quoteItem) {
                    if ($quoteItem->getProductId() == $id) {
                        $totalItemQty += $quoteItem->getQty();
                    }
                }

                $prices = $this->spotPricingApi->getSpotPrice([$sku]);

                if (!$prices) {
                    return [$item, $request, $candidate];
                }

                foreach ($prices as $key => $productPrices) {
                    $specialPrice = $this->arrayManager->get('DiscountPrice', $productPrices);
                    if ($specialPrice && $specialPrice < $product->getPrice()) {
                        $request->setCustomPrice($specialPrice);
                    }
                    $tierPrice = $this->productHelper->getTierPrice($prices, $sku, $totalItemQty);
                    if ($tierPrice) {
                        $request->setCustomPrice($tierPrice);
                    }
                }

            } catch (LocalizedException $e) {
                $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
            }
        }
        return [$item, $request, $candidate];
    }
}