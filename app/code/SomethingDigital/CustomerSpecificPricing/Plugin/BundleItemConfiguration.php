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
                $prices = $this->spotPricingApi->getSpotPrice([$selectionProduct->getSku()]);
                if (!$prices) {
                    return $result;
                }
                if (isset($prices[0]['DiscountPrice']) && $prices[0]['DiscountPrice'] < $result) {
                    return $price;
                }
            } catch (LocalizedException $e) {
                $this->logger->error("SomethingDigital_CustomerSpecificPricing: " . $e->getMessage());
            }
        }

        return $result;
    }
}