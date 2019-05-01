<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\ArrayManager;

class BundleItemConfiguration
{
    /**
     * @var SpotPricingApi
     */
    private $spotPricingApi;

    /**
    * @var CustomerSession
    */
    private $customerSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    public function __construct(
        SpotPricingApi $spotPricingApi,
        Session $customerSession,
        LoggerInterface $logger,
        ArrayManager $arrayManager
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->arrayManager = $arrayManager;
    }

    public function aroundGetSelectionFinalPrice(Configuration $subject, \Closure $proceed, $item, $selectionProduct)
    {
        $result = $proceed($item, $selectionProduct);

        if ($this->customerSession->isLoggedIn()) {
            try {
                $prices = $this->spotPricingApi->getSpotPrice($selectionProduct->getSku());
                $price = $this->arrayManager->get('body/Price', $prices);
                if ($price && $price < $result) {
                    return $price;
                }
            } catch (LocalizedException $e) {
                $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
            }
        }

        return $result;
    }
}