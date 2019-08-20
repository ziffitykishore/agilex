/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/error-processor'
], function (resourceUrlManager, quote, storage, shippingService, rateRegistry, errorProcessor) {
    'use strict';

    return {
        /**
         * @param {Object} address
         */
        getRates: function (address) {
            var cache;
            var mageCache = localStorage.getItem('mage-cache-storage');
            var mageCacheToJson = JSON.parse(mageCache);
            shippingService.isLoading(true);
            var shippingData = mageCacheToJson['checkout-data']['shippingAddressFromData'];
            var billingData = mageCacheToJson['checkout-data']['billingAddressFromData']
            var pickupDate = '';
            var pickupTime = '';
            var pickupComment = '';
            if(shippingData !== null ) {
                pickupDate = shippingData["pickupdate_date"];
                pickupTime = shippingData["pickupdate_time"];
                pickupComment =shippingData["pickupdate_comment"];
            }
            if(billingData !== null || pickupDate === undefined) {
                if (pickupDate === '') {
                    pickupDate = billingData["pickupdate_date"];
                    pickupTime = billingData["pickupdate_time"];
                    pickupComment = billingData["pickupdate_comment"];
                }
            }
            if(pickupDate === '' || pickupDate === undefined) {
                    pickupDate = localStorage.getItem(window.checkoutConfig.storeCode+"_selectedPickupDate");
                    pickupTime = localStorage.getItem(window.checkoutConfig.storeCode+"_selectedPickupTime");
                    pickupComment = localStorage.getItem(window.checkoutConfig.storeCode+"_selectedPickupComment");
            }
            cache = rateRegistry.get(address.getKey());

            if (cache) {
                shippingService.setShippingRates(cache);
                shippingService.isLoading(false);
            } else {
                storage.post(
                    resourceUrlManager.getUrlForEstimationShippingMethodsByAddressId(),
                    JSON.stringify({
                        addressId: address.customerAddressId,
                        data: {
                                'date': pickupDate,
                                'tinterval_id': pickupTime,
                                'comment': pickupComment
                            }
                    }),
                    false
                ).done(function (result) {
                    rateRegistry.set(address.getKey(), result);
                    shippingService.setShippingRates(result);
                }).fail(function (response) {
                    shippingService.setShippingRates([]);
                    errorProcessor.process(response);
                }).always(function () {
                    shippingService.isLoading(false);
                }
                );
            }
        }
    };
});
