define([
    'jquery',
], function ($) {
    return function (data, currencySymbol, productMap) {
        for (var productId in productMap) {
            var sku = productMap[productId];
            if (is_array(data[sku]) && isset(data[sku]['price']) && data[sku]['price'] != 0 && data[sku]['price'] != null) {
                var price = data[sku]['price'];
                var priceBoxId = "#product-price-" + productId;
                $el = $(priceBoxId + " .price");
                if ($el.length > 0) {
                    $el.text(currencySymbol + price);
                }
            }
        }
    }
});
