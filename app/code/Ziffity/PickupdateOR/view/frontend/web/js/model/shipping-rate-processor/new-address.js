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
         * Get shipping rates for specified address.
         * @param {Object} address
         */
        getRates: function (address) {
            var cache, serviceUrl, payload;
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
            if(billingData !== null) {
                if (pickupDate === '' || pickupDate === undefined) {
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
            cache = rateRegistry.get(address.getCacheKey());
            serviceUrl = resourceUrlManager.getUrlForEstimationShippingMethodsForNewAddress(quote);
            payload = JSON.stringify({
                    address: {
                        'street': address.street,
                        'city': address.city,
                        'region_id': address.regionId,
                        'region': address.region,
                        'country_id': address.countryId,
                        'postcode': address.postcode,
                        'email': address.email,
                        'customer_id': address.customerId,
                        'firstname': address.firstname,
                        'lastname': address.lastname,
                        'middlename': address.middlename,
                        'prefix': address.prefix,
                        'suffix': address.suffix,
                        'vat_id': address.vatId,
                        'company': address.company,
                        'telephone': address.telephone,
                        'fax': address.fax,
                        'custom_attributes': address.customAttributes,
                        'save_in_address_book': address.saveInAddressBook
                    },
                    data: {
                    'date': pickupDate,
                    'tinterval_id': pickupTime,
                    'comment': pickupComment
                    }
                }
            );

            if (cache) {
                shippingService.setShippingRates(cache);
                shippingService.isLoading(false);
            } else {
                storage.post(
                    serviceUrl, payload, false
                ).done(function (result) {
                    rateRegistry.set(address.getCacheKey(), result);
                    shippingService.setShippingRates(result);
                }).fail(function (response) {
                    shippingService.setShippingRates([]);
                    errorProcessor.process(response);
                }).always(function () {
                    shippingService.isLoading(false);
                });
            }
        }
    };
});
