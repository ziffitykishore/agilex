<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Cart;

class UpdateCustomerQuote
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
        LoggerInterface $logger,
        Cart $cart
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->cart = $cart;
    }

    public function afterLoadCustomerQuote(\Magento\Checkout\Model\Session $subject, $result)
    {
        $items = $this->cart->getQuote()->getAllItems();

        if ($items) {
            foreach ($items as $item) {
                $price = $item->getPrice();
                try {
                    if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                        $prices = $this->spotPricingApi->getSpotPrice($item->getSku());
                        $spotPrice = $prices['body']['Price'];
                        if ($spotPrice != 0 && $spotPrice < $price) {
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

        return $result;
    }
}
