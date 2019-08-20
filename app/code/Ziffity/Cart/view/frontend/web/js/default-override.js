define(
    [
        'jquery',
        'underscore',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/select-billing-address'
    ],
    function (
        $,
        _,
        ko,
        quote,
        resourceUrlManager,
        storage,
        paymentService,
        methodConverter,
        errorProcessor,
        fullScreenLoader,
        selectBillingAddressAction
    ) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var payload;

                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }

                payload = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    }
                };

                this.extendPayload(payload);

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
            },

            extendPayload: function (payload) {


                if (!payload.addressInformation.hasOwnProperty('extension_attributes')) {
                    payload.addressInformation.extension_attributes = {};
                }

                
                var mageCache = localStorage.getItem('mage-cache-storage');
                var mageCacheToJson = JSON.parse(mageCache);
                var shippingData = mageCacheToJson['checkout-data']['shippingAddressFromData'];
                var billingData = mageCacheToJson['checkout-data']['billingAddressFromData']
                var deliveryDate = '';
                var deliveryTime = '';
                var deliveryComment = '';
                if (shippingData !== null) {
                    deliveryDate = shippingData["amdeliverydate_date"];
                    deliveryTime = shippingData["amdeliverydate_time"];
                    deliveryComment = shippingData["amdeliverydate_comment"];
                }
                if (billingData !== null) {
                    if (deliveryDate === '' || deliveryDate === undefined) {
                        deliveryDate = billingData["amdeliverydate_date"];
                        deliveryTime = billingData["amdeliverydate_time"];
                        deliveryComment = billingData["amdeliverydate_comment"];
                    }
                }
                if(deliveryDate === '' || deliveryDate === undefined) {
                    deliveryDate = localStorage.getItem(window.checkoutConfig.storeCode+"_selectedDeliveryDate");
                    deliveryTime = localStorage.getItem(window.checkoutConfig.storeCode+"_selectedDeliveryTime");
                    deliveryComment = localStorage.getItem(window.checkoutConfig.storeCode+"_selectedDeliverycomment");
                }
                var data = {
                    amdeliverydate_date: deliveryDate,
                    amdeliverydate_time: deliveryTime,
                    amdeliverydate_comment: deliveryComment
                };
                payload.addressInformation.extension_attributes = _.extend(
                    payload.addressInformation.extension_attributes,
                    data
                );
            }
        };
    }
);
