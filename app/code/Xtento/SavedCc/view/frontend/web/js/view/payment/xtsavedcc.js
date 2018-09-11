define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'xtsavedcc',
                component: 'Xtento_SavedCc/js/view/payment/method-renderer/xtsavedcc'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);