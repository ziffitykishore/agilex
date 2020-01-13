define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';    

    return function (address) {
 
        return wrapper.wrap(address, function (originalAction, addressData) {
            
            var obj = originalAction(addressData);
            obj.getType = function () {
                if (addressData['custom_attributes'].length != 0) {
                    if (addressData['custom_attributes']['is_billing']['value'] == 0) {
                        return 'customer-shipping-address';
                    }
                    else {
                        return 'customer-address';
                    }
                } else {
                    return false;
                }
            }
            obj.canUseForBilling = function () {
                let customer = window.customerData;
                let customerHasBillingAddress = false;
                for (address in customer.addresses) {
                    if (typeof customer.addresses[address].custom_attributes.is_billing != 'undefined') {
                        if (customer.addresses[address].custom_attributes.is_billing.value == 1) {
                            customerHasBillingAddress = true;
                        }
                    }
                }
                if (addressData['custom_attributes'].length != 0) {
                    if (addressData['custom_attributes']['is_billing']['value'] == 1 || !customerHasBillingAddress) {
                        return true;
                    }
                }
                return false;
            }
            return obj;
        });
    };
});