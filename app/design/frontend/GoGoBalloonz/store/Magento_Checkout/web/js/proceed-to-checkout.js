/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'mage/validation'
], function ($, authenticationPopup, customerData,validation) {
    'use strict';

    return function (config, element) {
        $(element).click(function (event) {
            var cart = customerData.get('cart'),
                customer = customerData.get('customer');

            event.preventDefault();

            if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                authenticationPopup.showModal();

                return false;
            }
            
            var pickupForm = $('#pickup-form');
            pickupForm.validation();
            if(!pickupForm.validation('isValid')) {
                return false;
            }
            
            var deliveryForm = $('#delivery-form');
            deliveryForm.validation();
            if(!deliveryForm.validation('isValid')) {
                return false;
            }
            
            var mageCache = localStorage.getItem('mage-cache-storage');
            var data = JSON.parse(mageCache);
            
            if (data["checkout-data"].shippingAddressFromData !== null) {

                let value = $('#pickupdate').hasClass('show');
                if (value) {
                    $('#delivery-form :input,text').val('');

                    if (data["checkout-data"].shippingAddressFromData.hasOwnProperty("amdeliverydate_date")) {
                        data["checkout-data"].shippingAddressFromData["amdeliverydate_date"] = "";
                    }
                    if (data["checkout-data"].shippingAddressFromData.hasOwnProperty("amdeliverydate_time")) {
                        data["checkout-data"].shippingAddressFromData["amdeliverydate_time"] = "";
                    }
                    if (data["checkout-data"].shippingAddressFromData.hasOwnProperty("amdeliverydate_comment")) {
                        data["checkout-data"].shippingAddressFromData["amdeliverydate_comment"] = "";
                    }
                    var str = JSON.stringify(data);
                    localStorage.setItem('mage-cache-storage', str);
                } else {
                    $('#pickup-form :input,text').val('');
                    if (data["checkout-data"].shippingAddressFromData.hasOwnProperty("pickupdate_date")) {
                        data["checkout-data"].shippingAddressFromData["pickupdate_date"] = "";
                    }
                    if (data["checkout-data"].shippingAddressFromData.hasOwnProperty("pickupdate_time")) {
                        data["checkout-data"].shippingAddressFromData["pickupdate_time"] = "";
                    }
                    if (data["checkout-data"].shippingAddressFromData.hasOwnProperty("pickupdate_comment")) {
                        data["checkout-data"].shippingAddressFromData["pickupdate_comment"] = "";
                    }
                    var str = JSON.stringify(data);
                    localStorage.setItem('mage-cache-storage', str);
                }
            }
            $(element).attr('disabled', true);
            location.href = config.checkoutUrl;
        });

    };
});
