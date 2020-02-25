<?php

namespace SomethingDigital\CustomerSpecificPricingRules\Plugin;

use Magento\SalesRule\Model\RulesApplier as RulesApplierModel;
use SomethingDigital\CustomerSpecificPricingRules\Model\QuoteItem;

class RulesApplier
{
    protected $quoteItem;

    public function __construct(
         QuoteItem $quoteItem
    ) {
        $this->quoteItem = $quoteItem;
    }

    public function beforeApplyRules(RulesApplierModel $subject, $item, $rules, $skipValidation, $couponCode)
    {
        $this->quoteItem->quoteItemHolder = $item;

        return [$item, $rules, $skipValidation, $couponCode];
    }
}
