define([
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/action/create-billing-address',
    'underscore'
], function(
    addressList,
    quote,
    checkoutData,
    createShippingAddress,
    selectShippingAddress,
    selectShippingMethodAction,
    paymentService,
    selectPaymentMethodAction,
    addressConverter,
    selectBillingAddress,
    createBillingAddress,
    _
) {
    'use strict';

    return function (target) {
        var applyBillingAddress = target.applyBillingAddress;

        target.applyBillingAddress = function () {
            var shippingAddress,
                isBillingAddressInitialized;

            if (quote.billingAddress()) {
                selectBillingAddress(quote.billingAddress());

                return;
            }

            if (quote.isVirtual()) {
                isBillingAddressInitialized = addressList.some(function (addrs) {
                    if (addrs.isDefaultBilling()) {
                        selectBillingAddress(addrs);

                        return true;
                    }

                    return false;
                });
            }

            shippingAddress = quote.shippingAddress();
        };

        return target;
    }
});