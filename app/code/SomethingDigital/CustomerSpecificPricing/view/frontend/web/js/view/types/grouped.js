define([
    'jquery',
], function ($) {
    return function (data, currencySymbol, productMap) {
        for (var productId in productMap) {
            var sku = productMap[productId];
            if (data[sku] !== undefined) {
                if (data[sku]['price'] !== 0 && data[sku]['price'] !== null) {
                    var price = data[sku]['price'];
                    var priceBoxId = "#product-price-" + productId;
                    var $el = $(priceBoxId + " .price");
                    $el.text(currencySymbol + price);
                }
            }
        }
    }
});
