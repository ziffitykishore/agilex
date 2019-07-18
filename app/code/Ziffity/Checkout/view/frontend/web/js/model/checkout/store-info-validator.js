define(
    [
        'jquery',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Checkout/js/model/error-processor'
    ],
    function ($, customer, quote, urlBuilder, urlFormatter, errorProcessor) {
        'use strict';

        return {

            /**
             * Make an ajax PUT request to store the order comment in the quote.
             *
             * @returns {Boolean}
             */
            validate: function () {
                var isCustomer = customer.isLoggedIn();
                var quoteId = quote.getQuoteId();
                var url;

                if (isCustomer) {
                    url = urlBuilder.createUrl('/carts/mine/set-store-info', {})
                } else {
                    url = urlBuilder.createUrl('/guest-carts/:cartId/set-store-info', {cartId: quoteId});
                }
                var address = '';
                if(window.checkoutConfig.pickup_store) {
                    address += "<p>" +  window.checkoutConfig.pickup_store['street'] + "</p>";
                    address += "<p>" +  window.checkoutConfig.pickup_store['city'] + "</p>";
                    address += "<p>" +  window.checkoutConfig.pickup_store['region_name'] + "</p>";
                    address += "<p>" +  window.checkoutConfig.pickup_store['country_id'] + "</p>";
                    address += "<p>" +  window.checkoutConfig.pickup_store['postcode'] + "</p>";
                    address += "<p>" +  window.checkoutConfig.pickup_store['phone'] + "</p>";
                }
                var location = '';
                if($.cookie('storeLocation')) {
                    location = JSON.parse($.cookie('storeLocation')).name;
                }
                console.log(location);
                console.log(address);
                var payload = {
                    cartId: quoteId,
                    orderInfo : {
                        storeAddress: address,
                        storeLocation : location
                    }
                    
                };

                if (!payload.orderInfo.storeAddress && !payload.orderInfo.storeAddress) {
                    return true;
                }

                var result = true;

                $.ajax({
                    url: urlFormatter.build(url),
                    data: JSON.stringify(payload),
                    global: false,
                    contentType: 'application/json',
                    type: 'PUT',
                    async: false
                }).done(
                    function (response) {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );

                return result;
            }
        };
    }
);