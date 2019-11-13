define([
    'jquery',
], function ($) {
    return function (data, currencySymbol) {
        $('#product-options-wrapper .bundle.option').each(function() {
            $('option',$(this)).each(function() {
                var sku = $(this).data('sku');
                var title = $(this).data('title');
                if (sku && data[sku]['price'] != 0 && title) {
                    $(this).text(title + ' +' + currencySymbol + data[sku]['price']);
                    $(this).attr('data-price', data[sku]['price']);
                }
            })
        });

        $('.price-box').on('updatePrice', function(e, info) {
            var totalPrice = 0;
            $('#product-options-wrapper .bundle.option').each(function() {
                var price = $(this).find(":selected").data('price');
                if (typeof price == 'undefined') {
                    return false;
                }
                totalPrice += parseFloat(price);
            });
            $('.price-configured_price span.price').text(currencySymbol+totalPrice.toFixed(2));
        });
        var pricesArr = Object.keys( data ).map(function ( key ) { return data[key]['price']; });
        var minPrice = $('.product-info-main .price-from .price-wrapper').data('price-amount');
        var maxPrice = $('.product-info-main .price-to .price-wrapper').data('price-amount');
        var minSpotPrice = Math.min.apply(null, pricesArr);
        var maxSportPrice = Math.max.apply(null, pricesArr);
        if (minSpotPrice != 0 && minSpotPrice < minPrice) {
            $('.product-info-main .price-from .price').text(currencySymbol + minSpotPrice);
        }
        if (maxSportPrice > maxPrice) {
            $('.product-info-main .price-to .price').text(currencySymbol + maxSportPrice);
        }
    }
});
