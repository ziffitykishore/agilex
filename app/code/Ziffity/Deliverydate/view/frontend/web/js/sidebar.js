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
                template: 'Amasty_Deliverydate/deliverydate-sidebar'
            },

            getLocalStorageData: function() {
                var mageCache = localStorage.getItem('mage-cache-storage');
                var mageCacheToJson = JSON.parse(mageCache);

                return mageCacheToJson['checkout-data']['shippingAddressFromData'];
            },

            getDeliveryDate: function() {
              var date = this.getLocalStorageData();
              if (date !== null) {
                if (date['amdeliverydate_date'] != null) {
                    return date['amdeliverydate_date'];
                }
              }
              return '';
            },

            getDeliveryTime: function() {
              var time = this.getLocalStorageData();
              if (time !== null) {
                if (time['amdeliverydate_time'] != null) {
                   return time['amdeliverydate_time'];
                }
             }
            },

            getDeliveryComment: function() {
              var comment = this.getLocalStorageData();
              if (comment !== null) {
                if (comment['amdeliverydate_comment'] != null) {
                   return comment['amdeliverydate_comment'];
                }
              }
                return '';
            },

            isModuleEnabled: function() {
                return window.checkoutConfig.amasty.deliverydate.moduleEnabled;
            }
        });
    }
);
