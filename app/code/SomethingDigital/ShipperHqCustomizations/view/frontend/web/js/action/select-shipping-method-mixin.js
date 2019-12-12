 define([
     'jquery',
     'mage/utils/wrapper',
     'Magento_Checkout/js/model/quote',
     'Magento_Checkout/js/model/cart/totals-processor/default'
 ], function ($, wrapper, quote, totalsDefaultProvider) {
     'use strict';

     return function (selectShippingMethodAction) {
         return wrapper.wrap(selectShippingMethodAction, function (originalAction) {
            totalsDefaultProvider.estimateTotals(quote.shippingAddress());
            return originalAction();
         });
     };
 });