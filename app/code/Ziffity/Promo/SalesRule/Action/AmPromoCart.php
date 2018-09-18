<?php

namespace Ziffity\Promo\SalesRule\Action;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule\Action\Discount;

class AmPromoCart implements Discount\DiscountInterface
{

    public function __construct(
        Discount\DataFactory $discountDataFactory
    )
    {
        $this->discountDataFactory = $discountDataFactory;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param AbstractItem $item
     * @param float $qty
     * @return Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        return $this->getDiscountData($item);
    }

    protected function getDiscountData(AbstractItem $item): Discount\Data
    {
        return $this->discountDataFactory->create([
            'amount' => $item->getDiscountAmount(),
            'baseAmount' => $item->getBaseDiscountAmount(),
            'originalAmount' => $item->getOriginalDiscountAmount(),
            'baseOriginalAmount' => $item->getBaseOriginalDiscountAmount()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fixQuantity($qty, $rule)
    {
        return $qty;
    }
}