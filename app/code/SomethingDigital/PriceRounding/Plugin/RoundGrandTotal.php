<?php

namespace SomethingDigital\PriceRounding\Plugin;

use Magento\Payment\Gateway\Data\Order\OrderAdapter;

class RoundGrandTotal
{
    public function afterGetGrandTotalAmount(OrderAdapter $subject, $result)
    {
        return round($result, 2);
    }
}
