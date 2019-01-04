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
                var percentSaved = Math.floor(((basePrice - finalPrice) / basePrice) * 100) + "%";
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
                var percentSaved = Math.floor(((Math.abs(finalPrice)) / basePrice) * 100) + "%";
                $('.percent-saved').text($.mage.__("You save") + " " + percentSaved);
                $('.percent-saved').show();
            });
        } else if (type == 'grouped') {
            var $priceBoxes = $('.grouped .price-box');
            for (var i = 0; i < $priceBoxes.length; i++) {
                var priceBox = $priceBoxes[i];
                // jQuery wrapper around dom element
                var $priceBox = $(priceBox);
                var prices = getPricesFromPriceBox($priceBox);
                if (!prices) {
                    continue;
                }
                var percentSaved = Math.floor(((prices.oldPrice - prices.finalPrice) / prices.oldPrice) * 100);
                if (percentSaved < 1) {
                    continue;
                }
                var $span = $('<span>', { "class": "percent-saved" });
                $span.text($.mage.__("You save") + " " + percentSaved + "%");
                $priceBox.after($span);
            }
        }

        /**
         * Returns an object containing the base and
         * discounted price
         *
         * If the price doesn't have an "old price" or
         * a "special price" then it will return null
         *
         * @param {jQuery} price box
         * @return {Object|null} 
         */
        function getPricesFromPriceBox($priceBox) {
            $finalPrice = $priceBox.find('.special-price .price');
            $oldPrice = $priceBox.find('.old-price .price');

            if ($finalPrice.length < 1 || $oldPrice.length < 1) {
                return null;
            }

            return {
                finalPrice: $finalPrice.text().replace(/\$/g, ''),
                oldPrice: $oldPrice.text().replace(/\$/g, ''),
            };
        }
    }
});

