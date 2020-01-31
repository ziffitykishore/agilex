define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';    

    return function (address) {
 
        return wrapper.wrap(address, function (originalAction, addressData) {
            
            var obj = originalAction(addressData);
            obj.canUseForBilling = function () {
                var customer = window.customerData;
                var customerHasBillingAddress = false;
                var address;
                if (customer.addresses !== undefined) {
                    for (address in customer.addresses) {
                        if (typeof customer.addresses[address].custom_attributes.is_billing !== 'undefined') {
                            if (customer.addresses[address].custom_attributes.is_billing.value == 1) {
                                customerHasBillingAddress = true;
                            }
                        }
                    }
                }
                if (customer.email === undefined || !customerHasBillingAddress) {
                    return true;
                } else {
                    return false;
                }
            }
            return obj;
        });
    };
});