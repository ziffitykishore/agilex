define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';
 
    return function(targetModule){
 
        var reloadPrice = targetModule.prototype._reloadPrice;
        var reloadPriceWrapper = wrapper.wrap(reloadPrice, function(original){
            var result = original();

            if(this.options.spConfig.sku[this.simpleProduct] != '') {
                $('[data-role="sku"]').html(this.options.spConfig.sku[this.simpleProduct]);
            }
            
            if(this.options.spConfig.item_no[this.simpleProduct] != '') {
                $('[data-role="item_no"]').html(this.options.spConfig.item_no[this.simpleProduct]);
            }            

            if(this.options.spConfig.case_height[this.simpleProduct] != '') {
                $('[data-role="case_height"]').html(this.options.spConfig.case_height[this.simpleProduct]);
            }

            if(this.options.spConfig.case_length[this.simpleProduct] != '') {
                $('[data-role="case_length"]').html(this.options.spConfig.case_length[this.simpleProduct]);
            }

            if(this.options.spConfig.case_weight[this.simpleProduct] != '') {
                $('[data-role="case_weight"]').html(this.options.spConfig.case_weight[this.simpleProduct]);
            }

            if(this.options.spConfig.case_width[this.simpleProduct] != '') {
                $('[data-role="case_width"]').html(this.options.spConfig.case_width[this.simpleProduct]);
            }
            return result;
        });
        targetModule.prototype._reloadPrice = reloadPriceWrapper;
        
        return targetModule;
    };
});
