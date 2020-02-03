define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';    

    return function (address) {
        return wrapper.wrap(address, function (originalAction, addressData) {
            var customer = window.customerData;
            var customerHasBillingAddress = false;
            var customerAddress;
            if (!addressData.custom_attributes) {
                addressData.custom_attributes = {};
            }
            for (customerAddress in customer.addresses) {
                if (typeof customer.addresses[customerAddress].custom_attributes.is_billing === 'object') {
                    if (customer.addresses[customerAddress].custom_attributes.is_billing.value == 1) {
                        customerHasBillingAddress = true;
                        break;
                    }
                }
            }
            if (!customerHasBillingAddress) {
                addressData.custom_attributes.is_billing = true;
            } else {
                addressData.custom_attributes.is_billing = false;
            }

            return originalAction(addressData);
        });
    };
});