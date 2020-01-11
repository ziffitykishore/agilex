define([
    'ko',
], function (ko) {
    'use strict';

    var mixin = {
        getCustomerCarrierAccountNumber: function () {
            let customer = window.customerData;

            if (typeof(customer.custom_attributes.customer_freight_account) != 'undefined') {
                return customer.custom_attributes.customer_freight_account.value;
            } else {
                return '';
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
