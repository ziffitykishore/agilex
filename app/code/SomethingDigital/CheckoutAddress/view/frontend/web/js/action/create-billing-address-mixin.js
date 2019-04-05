define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';    

    return function (address) {
        return wrapper.wrap(address, function (originalAction, addressData) {
            addressData['custom_attributes'] = {
                0 : {
                    'attribute_code' : 'is_billing',
                    'value' : true
                }
            };
            return originalAction(addressData);
        });
    };
});