define([
    'jquery'
], function ($) {
    'use strict';
    return function (config) {
        var settings = {
            method: 'POST',
            dataType: 'json',
            url: config.url,
            data: {
                products: config.data,
            }
        }

        return {
            /**
             * Sends ajax request to retrieve prices
             *
             * @return {Promise}
             */
            getPrices: function() {
                return $.ajax(settings);
            }
        }
    }
});
