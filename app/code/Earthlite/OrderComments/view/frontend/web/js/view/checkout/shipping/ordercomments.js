define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/translate'], function ($, ko, Component) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Earthlite_OrderComments/checkout/shipping/ordercomments'
        },
        ordercomments: ko.observable(),
        title: ko.observable(),
        
        /**
         * 
         * @return {ordercommentsL#4.ordercommentsAnonym$1}
         */
        initialize: function () {
            var self = this;
            var title = $.mage.__(window.checkoutConfig.order_comments_title);
            var commentValue = window.checkoutConfig.quoteData.order_comments;
            self.ordercomments(commentValue);
            self.title(title);
            this._super();
            return this;
        },
        
        /**
         * 
         * @return {Boolean}
         */
        isDisplayed: function () {
            return window.checkoutConfig.order_comments_status;
        }
    });
});
