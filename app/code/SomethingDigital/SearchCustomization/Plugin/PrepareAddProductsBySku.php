<?php

namespace SomethingDigital\SearchCustomization\Plugin;

use Magento\AdvancedCheckout\Model\Cart;

class PrepareAddProductsBySku
{

    public function beforePrepareAddProductsBySku(Cart $subject, $items)
    {
        foreach ($items as $key => $item) {
            $items[$key]['sku'] = trim($item['sku']);
        }

        return [$items];
    }
}
