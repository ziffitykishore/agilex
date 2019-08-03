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
                    address += "<span>" +  window.checkoutConfig.pickup_store['street'] + "</span><br/>";
                    address += "<span>" +  window.checkoutConfig.pickup_store['city'] + "</span><br/>";
                    address += "<span>" +  window.checkoutConfig.pickup_store['region_name'] + "</span><br/>";
                    address += "<span>" +  window.checkoutConfig.pickup_store['country_id'] + "</span><br/>";
                    address += "<span>" +  window.checkoutConfig.pickup_store['postcode'] + "</span><br/>";
                    address += "<span>" +  window.checkoutConfig.pickup_store['phone'] + "</span>";
                }
                
                var payload = {
                    cartId: quoteId,
                    orderInfo : {
                        storeAddress: address
                    }
                    
                };

                if (!payload.orderInfo.storeAddress) {
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