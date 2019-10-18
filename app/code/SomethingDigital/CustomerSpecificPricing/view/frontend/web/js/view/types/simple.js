define([
    'jquery',
    'mage/translate'
], function ($,$t) {
    return function (data, currencySymbol) {
        var sku = $('[itemprop=sku]').text();
        var price = data[sku]['price'];

        if (price != null && price != 0) {
            $('.product-info-main div.price-final_price > span:not(.old-price) .price').text(currencySymbol + price);
        }
    }
});