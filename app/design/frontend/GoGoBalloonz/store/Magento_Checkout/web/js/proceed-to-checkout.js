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

            $(element).attr('disabled', true);
            location.href = config.checkoutUrl;
        });

    };
});
