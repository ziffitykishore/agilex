define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/action/select-billing-address'
], function($, wrapper, addressList, selectBillingAddress) {
    'use strict';

    return function (checkoutDataResolver) {
        checkoutDataResolver.resolveBillingAddress = wrapper.wrap(
            checkoutDataResolver.resolveBillingAddress,
            function(_super) {
                var selectedBillingAddress = checkoutConfig.payment.billing_address.id;
                addressList.some(function (address) {
                    if (selectedBillingAddress === address.customerAddressId) {
                        selectBillingAddress(address);
                    }
                });
            }
        );

        return checkoutDataResolver;
    };
});