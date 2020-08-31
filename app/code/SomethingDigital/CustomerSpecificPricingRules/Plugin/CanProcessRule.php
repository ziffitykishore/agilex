<?php

namespace SomethingDigital\CustomerSpecificPricingRules\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\SalesRule\Model\Utility;
use SomethingDigital\CustomerSpecificPricingRules\Model\QuoteItem;

class CanProcessRule
{
    protected $config;
    protected $quoteItem;

    public function __construct(
         QuoteItem $quoteItem,
         ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteItem = $quoteItem;
        $this->config = $scopeConfig;
    }

    public function aroundCanProcessRule(Utility $subject, callable $proceed, $rule, $address)
    {
        $applyDiscountIfSuffix = $this->config->getValue('catalog/price/apply_cart_rules_if_suffix', ScopeInterface::SCOPE_STORE);
        $applyDiscountIfCSP = $this->config->getValue('catalog/price/apply_cart_rules_if_customer_specific', ScopeInterface::SCOPE_STORE);
        $applyDiscountIfCSTP = $this->config->getValue('catalog/price/apply_cart_rules_if_customer_specific_tier_price', ScopeInterface::SCOPE_STORE);

        $quoteItem = $this->quoteItem->quoteItemHolder;

        if (
            !$applyDiscountIfCSP &&
            $quoteItem->getIsCustomerSpecificPriceApplied() &&
            !$quoteItem->getIsCustomerSpecificTierPriceApplied() &&
            $rule->getSimpleFreeShipping() == 0 &&
            $rule->getSkuSuffix() == ''
        ) {
            return false;
        }

        if (
            !$applyDiscountIfCSTP &&
            $quoteItem->getIsCustomerSpecificTierPriceApplied() &&
            $rule->getSimpleFreeShipping() == 0 &&
            $rule->getSkuSuffix() == ''
        ) {
            return false;
        }

        if (
            !$applyDiscountIfSuffix &&
            $quoteItem->getQuote()->getSuffix() &&
            $rule->getSimpleFreeShipping() == 0 &&
            $rule->getSkuSuffix() == ''
        ) {
            return false;
        }

        return $proceed($rule, $address);
    }
}
