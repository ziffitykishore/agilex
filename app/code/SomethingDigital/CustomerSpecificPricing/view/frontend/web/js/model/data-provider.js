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
                var skus = [];
                if (type == 'related') {
                    skus = config.relatedProducts;
                } else if (type == 'upsell') {
                    skus = config.upsellProducts;
                } else if (type == 'crosssell') {
                    skus = config.crosssellProducts;
                } else {
                    skus = config.data;
                }
                var settings = {
                    method: 'POST',
                    dataType: 'json',
                    url: config.url,
                    data: {
                        products: skus,
                        type: type
                    }
                }

                return $.ajax(settings);
            }
        }
    }
});
