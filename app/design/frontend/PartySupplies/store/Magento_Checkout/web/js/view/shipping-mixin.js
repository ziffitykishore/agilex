define([
    'Magento_Checkout/js/model/cart/estimate-service'
], function (estimateService) {
    'use strict';

    var mixin = {
        initialize: function () {
            this._super();
        },

        warehouseAddress: function () {
            return window.checkoutConfig.warehouse;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});