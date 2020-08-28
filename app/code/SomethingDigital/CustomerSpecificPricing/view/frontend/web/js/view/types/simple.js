define([
    'jquery'
], function ($) {
    return function (data, currencySymbol) {
        var sku = $('[itemprop=sku]').text();

        if (data[sku] !== undefined && data[sku]['price'] !== undefined) {
            var price = data[sku]['price'];

            if (price !== null && price !== 0) {
                if (!$('.price-final_price .old-price').length) {
                    var oldPrice = $('.product-info-main div.price-final_price > span:not(.old-price) .price').text();
                    $('.product-info-main .price-box.price-final_price').append('<span class="old-price"><span class="price-wrapper"><span class="price">' + oldPrice + '</span></span></span>');
                }
                $('.product-info-main div.price-final_price > span:not(.old-price) .price').text(currencySymbol + price);
            }
        }
    }
});