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

            getLocalStorageData: function() {
                var mageCache = localStorage.getItem('mage-cache-storage');
                var mageCacheToJson = JSON.parse(mageCache);

                return mageCacheToJson['checkout-data']['shippingAddressFromData'];
            },

            getPickupDate: function() {
              var date = this.getLocalStorageData();
              if (date !== null) {
                if (date['pickupdate_date'] != null) {
                   return date['pickupdate_date'];
                }
              }
                return '';
            },

            getPickupTime: function() {
              var time = this.getLocalStorageData();
              if (time !== null) {
                if (time['pickupdate_time'] != null) {
                   return time['pickupdate_time'];
                }
              }
            },

            getPickupComment: function() {
              var comment = this.getLocalStorageData();
              if (comment !== null) {
                if (comment['pickupdate_comment'] != null) {
                   return comment['pickupdate_comment'];
                }
              }
                return '';
            },

            isModuleEnabled: function() {
                return window.checkoutConfig.ziffity.pickupdate.moduleEnabled;
            }
        });
    }
);
