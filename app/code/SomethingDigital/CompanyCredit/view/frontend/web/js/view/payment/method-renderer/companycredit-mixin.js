/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/customer-data',
        'Magento_Catalog/js/price-utils',
        'mage/validation',
        'mage/translate'
    ],
    function (Component, $, quote, customerData, priceUtils) {
        'use strict';

            var mixin = {

                /** @inheritdoc */
                setConfig: function () {
                    this.config = window.checkoutConfig;
                },
                /** @inheritdoc */
                getExceedLimitMessage: function () {
                    if(this.config.credit_message == 1){
                        return $.mage.__('The credit limit for %s is %s. It will be exceeded by %s with this order.')
                        .replace('%s', this.getCompanyName())
                        .replace('%s', this.getLimitFormatted())
                        .replace('%s', this.getExceedLimitAmount());
                    }
                },
    
                checkCreditMessage: function () {   
                    console.log(typeof this.config.credit_message);  
                    return this.config.credit_message;
                },
            };
    
            return function (target) {
                return target.extend(mixin);
            };        
    }
);
