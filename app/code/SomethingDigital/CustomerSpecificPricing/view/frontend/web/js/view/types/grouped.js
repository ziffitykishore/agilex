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
                    $el = $(priceBoxId + " .price");
                    if ($el.length > 0) {
                        $el.text(currencySymbol + price);
                    }
                }
            }
        }
    }
});
