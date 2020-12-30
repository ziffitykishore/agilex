/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote'
    ],
    function ($,Component, quote) {
        "use strict";
        var quoteItemData = window.checkoutConfig.quoteItemData;
        var inserted_id =  [];
        var store_delivery_value = [];
        var deliveryInfo = [];
        var intial = quote.shippingAddress();
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
                var shippingAddress = quote.shippingAddress();
                var shippingMethod = quote.shippingMethod();

                /** Check the Changes in shipping address */
                if(intial != shippingAddress){
                    intial = shippingAddress;
                    inserted_id =[];
                }
                if ($.inArray(quoteItem.item_id, inserted_id) == -1){

                    /** Insert the  quoteItem_id and response Data to array*/
                    inserted_id.push(parseInt(quoteItem.item_id));

                    if (!deliveryInfo.length) {
                        this.getDeliverInfoAjax().done(function (response, textStatus) {
                            if (response.data) {
                                deliveryInfo = response.data;
                                store_delivery_value[quoteItem.item_id] = deliveryInfo;
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            return '';
                        });
                    }
                }

                /** get the response data based the index(quoteItem_id) and assign to delivery Info*/
                if (store_delivery_value[quoteItem.item_id]) {
                    var item = self.getItem(quoteItem.item_id);
                    if (item.sku in store_delivery_value[quoteItem.item_id]) {

                        if (quote.shippingMethod() && typeof store_delivery_value[quoteItem.item_id][item.sku] != 'string' ) {
                            return store_delivery_value[quoteItem.item_id][item.sku][quote.shippingMethod().method_code];
                        } else if (typeof store_delivery_value[quoteItem.item_id][item.sku] == 'string') {
                            return store_delivery_value[quoteItem.item_id][item.sku];
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