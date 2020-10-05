define([
    'ko',
], function (ko) {
    'use strict';

    var mixin = {
        getCustomerCarrierAccountNumber: function () {
            var customer = window.customerData;

            if (customer.length < 1) {
                return '';
            }
            if (typeof(customer.custom_attributes.customer_freight_account) != 'undefined') {
                return customer.custom_attributes.customer_freight_account.value;
            } else {
                return '';
            }
        },
        getCustomerCarrierOptions: function () {
            var checkoutConfig = window.checkoutConfig;

            return checkoutConfig.shipperhq_customer_carrier_options
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
