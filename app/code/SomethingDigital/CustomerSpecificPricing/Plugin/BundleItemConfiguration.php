<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Psr\Log\LoggerInterface;

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

    public function __construct(
        SpotPricingApi $spotPricingApi,
        Session $customerSession,
        LoggerInterface $logger
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    public function aroundGetSelectionFinalPrice(Configuration $subject, \Closure $proceed, $item, $selectionProduct)
    {
        $result = $proceed($item, $selectionProduct);

        if ($this->customerSession->isLoggedIn()) {
            try {
                $prices = $this->spotPricingApi->getSpotPrice($selectionProduct->getSku());
                $price = $prices['body']['Price'];
            } catch (LocalizedException $e) {
                $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
            }
        }
        if ($price != 0 && $price < $result) {
            return $price;
        }

        return $result;
    }
}