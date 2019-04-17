define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';    

    return function (address) {
        return wrapper.wrap(address, function (originalAction, addressData) {
            if (typeof addressData['custom_attributes'] == 'undefined') {
                addressData['custom_attributes'] = [];
            }
            var isBillingExists = addressData['custom_attributes'].filter(function(attr) { 
                return attr.attribute_code === "is_billing";
            });
            if (isBillingExists.length < 1) {
                addressData['custom_attributes'].push({
                    'attribute_code' : 'is_billing',
                    'value' : true
                });
            }
            addressData['custom_attributes'].push({
                'attribute_code' : 'is_read_only',
                'value' : false
            })
            return originalAction(addressData);
        });
    };
});