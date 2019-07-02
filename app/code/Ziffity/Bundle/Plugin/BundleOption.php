<?php

namespace Ziffity\Bundle\Plugin;

class BundleOption
{
    public function aroundGetSelectionQtyTitlePrice(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $subject,
        \Closure $proceed,
        $selection,
        $includeContainer = true
    ) {

        $subject->setFormatProduct($selection);
        $priceTitle = '<span class="product-name">'
            . $subject->escapeHtml($selection->getName())
            . '</span>';

        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '') . '' .
            $subject->renderPriceString($selection, $includeContainer) . ($includeContainer ? '</span>' : '');

        return $priceTitle;
    }
}
