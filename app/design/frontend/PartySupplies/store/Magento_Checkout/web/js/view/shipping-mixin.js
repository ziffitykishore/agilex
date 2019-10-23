define([

], function () {
    'use strict';

    return function (Component) {
        return Component.extend({
            warehouseAddress: function () {
                return window.checkoutConfig.warehouse;
            }
        });
    };
});