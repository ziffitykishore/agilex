<?php
namespace SomethingDigital\PriceRounding\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;

class RoundPrice
{    
    public function aroundRound(\Magento\Directory\Model\PriceCurrency $subject, callable $proceed, $price)
    {
        return round($price, 4);
    }
}