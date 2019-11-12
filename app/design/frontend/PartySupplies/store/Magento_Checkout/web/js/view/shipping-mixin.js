define([
    'ko',
    'Magento_Checkout/js/model/cart/estimate-service',
    'Magento_Customer/js/model/address-list'
], function (ko,estimateService,addressList) {
    'use strict';

    var mixin = {
        canShowAddressBook: ko.computed(function () {
            return addressList().length>1;
        }),

        initialize: function () {
            this._super();
        },

        warehouseAddress: function () {
            return window.checkoutConfig.warehouse;
        },
        
        isVisible: function () {
            return true;
        },
        
        getTooltipMessage: function () {
            return window.checkoutConfig.ownShipping.tooltip;
        },
        
        getPalletMessage: function () {
            return window.checkoutConfig.palletShipping.tooltip;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});