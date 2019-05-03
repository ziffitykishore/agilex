define([
    'jquery',
], function ($) {
    return function (data, productMap) {
        for (var productId in productMap) {
            var sku = productMap[productId];
            var price = data[sku];
            var priceBoxId = "#product-price-" + productId;
            $el = $(priceBoxId + " .price");
            if (price != null && price != 0 && $el.length > 0) {
                $el.text('$' + price);
            }
        }
    }
});
