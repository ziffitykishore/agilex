define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'PartySupplies_PalletShipping/pallet-shipping-tooltip.html'
        },
        
        isVisible: function () {
            return true;
        },
        
        getTooltipMessage: function () {
            return window.checkoutConfig.palletShipping.tooltip;
        }
    });
});
