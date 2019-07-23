define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Ulmod_OrderComment/js/model/checkout/order-comment-validator',
        'Ziffity_Checkout/js/model/checkout/store-info-validator'
    ],
    function (Component, additionalValidators, commentValidator,storeInfoValidator) {
        'use strict';

        additionalValidators.registerValidator(commentValidator);
        additionalValidators.registerValidator(storeInfoValidator);
        return Component.extend({});
    }
);