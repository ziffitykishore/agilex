define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function ($, ko, Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'PartySupplies_OwnShipping/own-shipping-info.html'
        },

        initObservable: function () {

            this.selectedMethod = ko.computed(function() {
                var method = quote.shippingMethod();
                var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                console.log(selectedMethod);
                return selectedMethod;
            }, this);

            return this;
        },

        warehouseAddress: function () {
            return window.checkoutConfig.warehouse;
        }
    });
});