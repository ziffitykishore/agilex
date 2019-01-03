define([
    'jquery',
    'mage/translate'
], function ($) {
    return function (config) {
        var type = config.type;
        var basePrice = config.basePrice;
        var finalPrice = config.finalPrice;
        if (type == 'simple') {
            basePrice = config.basePrice;
            finalPrice = config.finalPrice;
            if (finalPrice == basePrice || finalPrice === 0) {
                $('.percent-saved').hide();
                $('.percent-saved').text("");
            } else {
                var percentSaved = Math.round(((basePrice - finalPrice) / basePrice) * 100) + "%";
                $('.percent-saved').text($.mage.__("You save") + " " + percentSaved);
                $('.percent-saved').show();
            }
        } else if (type == 'configurable') {
            $('.price-final_price').on('updatePrice', function(e, p) {
                // Note that the "finalPrice.amount" is the number "offset" from the basePrice
                // e.x. if special price = 45 and base price = 100, finalPrice.amount = -55
                finalPrice = p.prices.finalPrice.amount;
                if (finalPrice == 0) {
                    $('.percent-saved').hide();
                    $('.percent-saved').text("");
                   return; 
                }
                var percentSaved = Math.round(((Math.abs(finalPrice)) / basePrice) * 100) + "%";
                $('.percent-saved').text($.mage.__("You save") + " " + percentSaved);
                $('.percent-saved').show();
            });
        } else if (type == 'grouped') {
            var $priceBoxes = $('.grouped .price-box');
            for (var priceBox of $priceBoxes) {
                // call a function to calculate $(priceBox)
            }
        }
    }
});

