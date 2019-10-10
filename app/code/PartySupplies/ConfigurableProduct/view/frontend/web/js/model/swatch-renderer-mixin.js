define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
	'use strict';
 
    return function(targetModule){
        
        var updatePrice = targetModule.prototype._UpdatePrice;
        var updatePriceWrapper = wrapper.wrap(updatePrice, function(original){

            var allSelected = true;
            var swatchSelector = 'div.product-info-main .product-options-wrapper .swatch-attribute.';

            for(var i=0; i<this.options.jsonConfig.attributes.length; i++){
                
                if (!$(swatchSelector + this.options.jsonConfig.attributes[i].code).attr('option-selected')){
                	allSelected = false;
                }
            }
            if (allSelected){
                var products = this._CalcProducts();
            }
            var productId = products ? products.shift() : null;
            
            if (productId) {
                if(this.options.jsonConfig.sku[productId] != '') {
                    $('[data-role="sku"]').html(this.options.jsonConfig.sku[productId]);
                }            

                if(this.options.jsonConfig.item_no[productId] != '') {
                    $('[data-role="item_no"]').html(this.options.jsonConfig.item_no[productId]);
                }

                if(this.options.jsonConfig.case_height[productId] != '') {
                    $('[data-role="case_height"]').html(this.options.jsonConfig.case_height[productId]);
                }

                if(this.options.jsonConfig.case_length[productId] != '') {
                    $('[data-role="case_length"]').html(this.options.jsonConfig.case_length[productId]);
                }

                if(this.options.jsonConfig.case_weight[productId] != '') {
                    $('[data-role="case_weight"]').html(this.options.jsonConfig.case_weight[productId]);
                }

                if(this.options.jsonConfig.case_width[productId] != '') {
                    $('[data-role="case_width"]').html(this.options.jsonConfig.case_width[productId]);
                }
            }
            return original();
        });
 
        targetModule.prototype._UpdatePrice = updatePriceWrapper;
        return targetModule;
    };
});
