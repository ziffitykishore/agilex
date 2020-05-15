/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent'
], function (Component) {
    'use strict';

    var quoteItemData = window.checkoutConfig.quoteItemData;
    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/item/details'
        },

        /**
         * @param {Object} quoteItem
         * @return {String}
         */
        getValue: function (quoteItem) {
            return quoteItem.name;
        },

        getEstimateShip: function(quoteItem) {
            var item = this.getItem(quoteItem.item_id);
            return item.custom_ship_estimation;
        },

        getShippingText: function(quoteItem) {
            var item = this.getItem(quoteItem.item_id);            
            if(item.item_productivity)
            {
                return item.production_item_text;
            }
            else
            {
                return item.nonproduction_item_text;   
            }
        },

        getItem: function(item_id) {
            var itemElement = null;
            _.each(quoteItemData, function(element, index) {                
                if (element.item_id == item_id) {
                    itemElement = element;
                }
            });
            return itemElement;
        }
    });
});
