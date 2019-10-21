define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'payonaccount',
            component: 'PartySupplies_PayOnAccount/js/view/payment/method-renderer/payonaccount-method'
        }
    );

    return Component.extend({});
});
