define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';    

    return function (address) {
 
        return wrapper.wrap(address, function (originalAction, addressData) {
            
            var obj = originalAction(addressData);
            obj.getType = function () {
                if (addressData['custom_attributes'].length != 0) {
                    if (addressData['custom_attributes']['is_billing']['value'] == 0)
                        return 'customer-shipping-address';
                    else
                        return 'customer-address';
                }
            }
            obj.canUseForBilling = function () {
                if (addressData['custom_attributes'].length != 0) {
                    if (addressData['custom_attributes']['is_billing']['value'] == 0)
                        return false;
                    else
                        return true;
                }
            }
            return obj;
        });
    };
});