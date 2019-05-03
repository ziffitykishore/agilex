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

    public function __construct (
        CustomerRepositoryInterface $customerRepo,
        ProductRepositoryInterface $productRepo,
        Session $session,
        LoggerInterface $logger, 
        ProductHelper $productHelper,
        SpotPricingApi $spotPricingApi,
        ArrayManager $arrayManager
    ) {
        $this->customerRepo = $customerRepo;
        $this->productRepo = $productRepo;
        $this->session = $session;
        $this->logger = $logger;
        $this->productHelper = $productHelper;
        $this->spotPricingApi = $spotPricingApi;
        $this->arrayManager = $arrayManager;
    }

    public function beforePrepare(
        Processor $subject,
        Item $item, 
        DataObject $request, 
        Product $candidate
    ) {
        $id = $candidate->getId();
        try {
            /** @var ProductInterface $product */
            $product = $this->productRepo->getById($id);
        } catch (LocalizedException $e) {
            return [$item, $request, $candidate];
        }
        
        $sku = $product->getSku();
        /** @var int $qty */
        $qty = $candidate->getCartQty();


        try {
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customerData */
            $customerData = $this->customerRepo->getById($this->session->getCustomerId());
        } catch (LocalizedException $e) {
            return [$item, $request, $candidate];
        }
        if ($this->session->isLoggedIn()) {
            try { 
                $prices = $this->spotPricingApi->getSpotPrice($sku);
                $price = $this->arrayManager->get('body/Price', $prices);
                if ($price && $price < $product->getPrice()) {
                    $request->setCustomPrice($price);
                }
            } catch (LocalizedException $e) {
                $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
                return [$item, $request, $candidate];
            }
        }
        return [$item, $request, $candidate];
    }
}
