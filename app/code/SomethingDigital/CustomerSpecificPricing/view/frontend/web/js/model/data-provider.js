define([
    'jquery'
], function ($) {
    'use strict';
    return function (config) {

        return {
            /**
             * Sends ajax request to retrieve prices
             *
             * @return {Promise}
             */
            getPrices: function(type) {
                if (type == 'related') {
                    var skus = config.relatedProducts;
                } else if (type == 'upsell') {
                    var skus = config.upsellProducts;
                } else if (type == 'crosssell') {
                    var skus = config.crosssellProducts;
                } else {
                    var skus = config.data;
                }
                var settings = {
                    method: 'POST',
                    dataType: 'json',
                    url: config.url,
                    data: {
                        products: skus,
                    }
                }

                return $.ajax(settings);
            }
        }
    }
});
