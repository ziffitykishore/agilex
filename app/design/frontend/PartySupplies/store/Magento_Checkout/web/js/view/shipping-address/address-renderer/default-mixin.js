define([
], function () {
    'use strict';

    var mixin = {
        selected_address: 'Magento_Checkout/shipping-address/address-renderer/selected_address'
    };

    return function (target) {
        return target.extend(mixin);
    };
});