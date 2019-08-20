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
                var shippingData = mageCacheToJson['checkout-data']['shippingAddressFromData'];
                var billingData = mageCacheToJson['checkout-data']['billingAddressFromData'];
                if(shippingData !== null && shippingData['pickupdate_date'] !== '') {
                    return shippingData;
                }else if(billingData !== null && billingData['pickupdate_date'] !== ''){
                    return billingData;
                }else{
                    return null;
                }
            },

            isPickup: function() {
                return quote.isVirtual();
            },

            getPickupDate: function() {
              var date = this.getLocalStorageData();
              if (date !== null) {
                if (date['pickupdate_date'] !== null) {
                   return date['pickupdate_date'];
                }
              }else{
                  return localStorage.getItem(window.checkoutConfig.storeCode+'_selectedPickupDate');
              }
                return '';
            },

            getPickupTime: function() {
              var time = this.getLocalStorageData();
              var timeSlot = JSON.parse(localStorage.getItem(window.checkoutConfig.storeCode+'_pickupSlots'));
              if (time !== null) {
                if (time['pickupdate_time'] !== null) {
                    var selectedTimeSlot = timeSlot.filter(function(obj) {
                        return obj.value === time['pickupdate_time'];
                    });
                    return selectedTimeSlot[0].label;
                }
              }else{
                   var selectedTimeSlot = timeSlot.filter(function(obj) {
                       return obj.value === localStorage.getItem(window.checkoutConfig.storeCode+'_selectedPickupTime');
                   });
                   return selectedTimeSlot[0].label;
              }
              return '';
            },

            getPickupComment: function() {
              var comment = this.getLocalStorageData();
              if (comment !== null) {
                if (comment['pickupdate_comment'] !== null) {
                   return comment['pickupdate_comment'];
                }
              }else{
                  return localStorage.getItem(window.checkoutConfig.storeCode+'_selectedPickupComment');
              }
                return '';
            },

            isModuleEnabled: function() {
                return window.checkoutConfig.ziffity.pickupdate.moduleEnabled;
            }
        });
    }
);
