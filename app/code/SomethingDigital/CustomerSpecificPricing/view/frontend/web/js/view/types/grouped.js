define([
    'jquery',
], function ($) {
    return function (data, currencySymbol, productMap) {
        for (var productId in productMap) {
            var sku = productMap[productId];
            var price = data[sku]['price'];
            var priceBoxId = "#product-price-" + productId;
            $el = $(priceBoxId + " .price");
            if (price != null && price != 0 && $el.length > 0) {
                $el.text(currencySymbol + price);
            }
        }
    }
});
