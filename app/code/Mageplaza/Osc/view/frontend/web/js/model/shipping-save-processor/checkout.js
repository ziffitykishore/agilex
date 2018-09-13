/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'ko',
        'jquery',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Mageplaza_Osc/js/model/resource-url-manager',
        'mage/storage',
        'Mageplaza_Osc/js/model/osc-data',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/select-billing-address'
    ],
    function (ko,
              $,
              _,
              quote,
              resourceUrlManager,
              storage,
              oscData,
              paymentService,
              methodConverter,
              errorProcessor,
              fullScreenLoader,
              selectBillingAddressAction) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var payload,
                    addressInformation = {},
                    additionInformation = oscData.getData();
                if (window.checkoutConfig.oscConfig.giftMessageOptions.isOrderLevelGiftOptionsEnabled) {
                    additionInformation.giftMessage = this.saveGiftMessage();
                }
                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }

                if (!quote.isVirtual()) {
                    addressInformation = {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    };
                } else if ($.isEmptyObject(additionInformation)) {
                    return $.Deferred().resolve();
                }

                var customAttributes = {};
                if (_.isObject(quote.billingAddress().customAttributes)) {
                    _.each(quote.billingAddress().customAttributes, function (attribute, key) {
                        if (_.isObject(attribute)) {
                            customAttributes[attribute.attribute_code] = attribute.value
                        } else if (_.isString(attribute)) {
                            customAttributes[key] = attribute
                        }
                    });
                }

                payload = {
                    addressInformation: addressInformation,
                    customerAttributes: customAttributes,
                    additionInformation: additionInformation
                };

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetCheckoutInformation(quote),
                    JSON.stringify(payload)
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        fullScreenLoader.stopLoader();
                    }
                );
            },
            saveGiftMessage: function () {
                var giftMessage = {};
                if (!$("#osc-gift-message").is(":checked")) $('.gift-options-content').find('input:text,textarea').val('');
                giftMessage.sender = $("#gift-message-whole-from").val();
                giftMessage.recipient = $("#gift-message-whole-to").val();
                giftMessage.message = $("#gift-message-whole-message").val();
                return JSON.stringify(giftMessage);
            }
        };
    }
);
