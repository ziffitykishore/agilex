define([
    'jquery'
], function ($) {
    return function (data, currencySymbol) {
        var sku = $('[itemprop=sku]').text();

        if (data[sku]['price'] !== undefined) {
            var price = data[sku]['price'];

            if (price !== null && price !== 0) {
                $('.product-info-main div.price-final_price > span:not(.old-price) .price').text(currencySymbol + price);
            }
        }
    }
});