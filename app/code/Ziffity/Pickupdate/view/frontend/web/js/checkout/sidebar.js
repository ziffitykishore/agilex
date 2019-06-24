define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote'
    ],
    function($, Component, quote) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ziffity_Pickupdate/pickupdate-sidebar'
            },

            getPickupDate: function() {
               if (quote.ziffityPickupDate && quote.ziffityPickupDate.dateFormated) {
                   return quote.ziffityPickupDate.dateFormated;
               }
                return '';
            },

            getPickupTime: function() {
               if (quote.ziffityPickupDate && quote.ziffityPickupDate.time) {
                   return quote.ziffityPickupDate.time;
               }
                return '';
            },

            getPickupComment: function() {
               if (quote.ziffityPickupDate && quote.ziffityPickupDate.comment) {
                   return quote.ziffityPickupDate.comment;
               }
                return '';
            },

            isModuleEnabled: function() {
                return window.checkoutConfig.ziffity.pickupdate.moduleEnabled;
            }
        });
    }
);
