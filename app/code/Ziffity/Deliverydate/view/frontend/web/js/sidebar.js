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
                if (!quote.isVirtual()) {
                    return mageCacheToJson['checkout-data']['shippingAddressFromData'];
                }
                return mageCacheToJson['checkout-data']['billingAddressFromData'];
                    
            },
            
            isPickup: function() {
                return quote.isVirtual();  
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
                    var timeSlot = JSON.parse(localStorage.getItem('deliverySlots'));
                    console.log(timeSlot);
                    var selectedTimeSlot = timeSlot.filter(obj=>obj.value === time['amdeliverydate_time']);
                    console.log(selectedTimeSlot);
                    return selectedTimeSlot[0].label;
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
                    var timeSlot = JSON.parse(localStorage.getItem('pickupSlots'));
                    console.log(timeSlot);
                    var selectedTimeSlot = timeSlot.filter(obj=>obj.value === time['pickupdate_time']);
                    console.log(time['pickupdate_time']);
                    return selectedTimeSlot[0].label;
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
                return window.checkoutConfig.amasty.deliverydate.moduleEnabled;
            }
        });
    }
);
