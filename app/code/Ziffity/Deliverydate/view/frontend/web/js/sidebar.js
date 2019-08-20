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

            getDeliveryDate: function() {
              var date = this.getLocalStorageData();
              if (date !== null) {
                if (date['amdeliverydate_date'] !== null) {
                   return date['amdeliverydate_date'];
                }
              }else{
                   return localStorage.getItem(window.checkoutConfig.storeCode+'_selectedDeliveryDate');
              }
              return '';
            },

            getDeliveryTime: function() {
              var time = this.getLocalStorageData();
              var timeSlot = JSON.parse(localStorage.getItem(window.checkoutConfig.storeCode+'_deliverySlots'));
              if (time !== null) {
                if (time['amdeliverydate_time'] !== null) {
                    var selectedTimeSlot = timeSlot.filter(function(obj) {
                        return obj.value === time['amdeliverydate_time'];
                    });
                    return selectedTimeSlot[0].label;
                }
             }else{
                    var selectedTimeSlot = timeSlot.filter(function(obj) {
                        return obj.value === localStorage.getItem(window.checkoutConfig.storeCode+'_selectedDeliveryTime');
                    });
                    return selectedTimeSlot[0].label;
             }
            },

            getDeliveryComment: function() {
              var comment = this.getLocalStorageData();
              if (comment !== null) {
                if (comment['amdeliverydate_comment'] !== null) {
                   return comment['amdeliverydate_comment'];
                }else{
                   return localStorage.getItem(window.checkoutConfig.storeCode+'_selectedDeliveryComment');
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
