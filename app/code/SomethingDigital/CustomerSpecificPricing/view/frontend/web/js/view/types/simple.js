define([
    'jquery'
], function ($) {
    return function (data) {
        var sku = $('[itemprop=sku]').text();
        var price = data[sku];
        if (price != null && price != 0) {
            $('.product-info-main .price').text('$' + price);
        }
    }
});
