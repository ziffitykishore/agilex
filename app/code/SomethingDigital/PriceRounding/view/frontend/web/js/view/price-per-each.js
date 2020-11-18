define([
    'jquery'
], function ($) {
    return function (pricePerEach) {
        if ($('.product-info-main .old-price').length) {
            $('.product-info-main span:not(.old-price) .price-container.price-final_price').append(pricePerEach);
        } else {
            $('.product-info-main .price-container.price-final_price').append(pricePerEach);
        }
    }
});
