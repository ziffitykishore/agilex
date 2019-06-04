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
                quote.ziffityPickupDate = [];
                quote.ziffityPickupDate.date = quote.ziffityPickupDateDate || $('[name="pickupdate_date"]').val();
                quote.ziffityPickupDate.dateFormated = $('[name="pickupdate_date"]').val();
                quote.ziffityPickupDate.time = $('[name="pickupdate_time"]').val() ?
                    $('[name="pickupdate_time"] option:selected').text() : '';
                quote.ziffityPickupDate.comment = $('[name="pickupdate_comment"]').val();

                var pickupData = {
                    pickupdate_date: quote.ziffityPickupDate.date,
                    pickupdate_time: $('[name="pickupdate_time"]').val(),
                    pickupdate_comment: quote.ziffityPickupDate.comment
                };

                if (!payload.addressInformation.hasOwnProperty('extension_attributes')) {
                    payload.addressInformation.extension_attributes = {};
                }

                payload.addressInformation.extension_attributes = _.extend(
                    payload.addressInformation.extension_attributes,
                    pickupData
                );
            }
        };
    }
);
