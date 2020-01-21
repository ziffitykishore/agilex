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
        var deliveryInfo = [];
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
                if (!deliveryInfo.length) {
                    this.getDeliverInfoAjax().done(function (response, textStatus) {
                        if (response.data) {
                            deliveryInfo = response.data;
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        return '';
                    });
                }
                if (deliveryInfo) {
                    var item = self.getItem(quoteItem.item_id);
                    if (item.sku in deliveryInfo) {
                        if (quote.shippingMethod() && typeof deliveryInfo[item.sku] != 'string' ) {
                            return deliveryInfo[item.sku][quote.shippingMethod().method_code];
                        } else if (typeof deliveryInfo[item.sku] == 'string') {
                            return deliveryInfo[item.sku];
                        }
                    }
                }
                return '';
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