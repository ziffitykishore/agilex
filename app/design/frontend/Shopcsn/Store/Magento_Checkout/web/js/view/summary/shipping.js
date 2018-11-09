define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/cart/estimate-service'
], function ($, Component, quote, estimateService) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/shipping'
        },
        quoteIsVirtual: quote.isVirtual(),
        totals: quote.getTotals(),

        /**
         * @return {*}
         */
        getShippingMethodTitle: function () {
            var shippingMethod;

            if (!this.isCalculated()) {
                return '';
            }
            shippingMethod = quote.shippingMethod();

            return shippingMethod ? shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'] : '';
        },

        /**
         * @return {*|Boolean}
         */
        isCalculated: function () {
            return this.totals() && this.isFullMode() && quote.shippingMethod() != null; //eslint-disable-line eqeqeq
        },

        /**
         * @return {*}
         */
        getValue: function () {
            var price;

            if (!this.isCalculated()) {
                return this.notCalculatedMessage;
            }
            /*TODO: work around to fix when shipping method not set*/
            /*price = this.totals()['shipping_amount'];*/
            var shippingMethod = quote.shippingMethod();
            var price = shippingMethod.amount;
            if(!price || parseInt(price) == 0 || parseInt(price) == 'NAN'){
                console.log('custom shipping calculation updated');
                var chCnf = window.checkoutConfig;
                price = chCnf.shippingMethods[0].amount;
                var total = this.getFormattedPrice(price + parseFloat(window.checkoutConfig.totalsData.subtotal));
                setTimeout(function(){
                    $('.grand.totals .price').text(total);
                }, 2000);
            }
            return this.getFormattedPrice(price);
        }
    });
});
