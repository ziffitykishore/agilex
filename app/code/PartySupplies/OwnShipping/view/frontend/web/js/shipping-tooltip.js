define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'PartySupplies_OwnShipping/own-shipping-tooltip.html'
        },
        
        isVisible: function () {
            return true;
        },
        
        getTooltipMessage: function () {
            return window.checkoutConfig.ownShipping.tooltip;
        }
    });
});
