/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
define(
    [
        'uiComponent'
    ],
    function (Component) {
        "use strict";
        var quoteItemData = window.checkoutConfig.quoteItemData;
        return Component.extend({
            defaults: {
                template: 'SomethingDigital_ShipperHqCustomizations/summary/item/details'
            },
            quoteItemData: quoteItemData,
            getValue: function(quoteItem) {
                return quoteItem.name;
            },
            getDeliveryInfo: function(quoteItem) {
                var item = this.getItem(quoteItem.item_id);
                return item.deliveryInfo;
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