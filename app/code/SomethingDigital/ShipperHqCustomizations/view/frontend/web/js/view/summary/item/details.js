/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'ko'
    ],
    function ($,Component, quote,ko) {
        "use strict";
        var quoteItemData = window.checkoutConfig.quoteItemData;
        var deliveryInfo = ko.observable(null);
        return Component.extend({
            defaults: {
                template: 'SomethingDigital_ShipperHqCustomizations/summary/item/details'
            },
            quoteItemData: quoteItemData,
            getValue: function(quoteItem) {
                return quoteItem.name;
            },
            getDeliverInfoAjax: function() {
                var settings = {
                    method: 'POST',
                    dataType: 'json',
                    url: '/deliverydates/deliveryinfo/index'
                };
                return $.ajax(settings);
            },
            getDeliveryInfo: function(quoteItem) {
                var self = this;
                this.getDeliverInfoAjax().done(function (response, textStatus) {
                    var item = self.getItem(quoteItem.item_id);
                    if (response.data) {
                        if (item.sku in response.data) {
                            if (quote.shippingMethod() && typeof response.data[item.sku] != 'string' ) {
                                deliveryInfo(response.data[item.sku][quote.shippingMethod().method_code]);
                            } else if (typeof deliveryInfo[item.sku] == 'string') {
                                deliveryInfo(response.data[item.sku]);
                            }
                        }
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    deliveryInfo(null);
                });

                return deliveryInfo;
            },
            getItem: function(item_id) {
                var itemElement = null;
                _.each(this.quoteItemData, function(element, index) {
                    if(element.item_id == item_id) {
                        itemElement = element;
                    }
                })
                return itemElement;
            }
        });
    }
);