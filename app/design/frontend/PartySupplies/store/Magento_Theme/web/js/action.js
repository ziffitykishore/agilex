define([
    "jquery",
    "jquery/ui"
], function($) {
    "use strict";
 
    $.widget('qty.incdec', {
        _create: function() {
            this.increaseQty();
            this.decreaseQty();
            this.validateQty();
        },
        
        increaseQty: function() {
            $(".increaseQty").unbind().click(function(){
                var data = JSON.parse($(this).prev().attr('data-advanced-inventory'));
                var qty = 1;
                if (data["validate-item-quantity"]["qtyIncrements"]) {
                    qty = data["validate-item-quantity"]["qtyIncrements"];
                }
                $(this).prev().val(
                    parseInt($(this).prev().val()) + qty
                );
            });
        },
        
        decreaseQty: function() {
            $(".decreaseQty").unbind().click(function(){
                var data = JSON.parse($(this).next().attr('data-advanced-inventory'));
                var qty = 1;
                if (data["validate-item-quantity"]["qtyIncrements"]) {
                    qty = data["validate-item-quantity"]["qtyIncrements"];
                }                
                if(parseInt($(this).next().val()) > qty) {
                    $(this).next().val(
                        parseInt($(this).next().val()) - qty
                    );                    
                }
            });
        },
        
        validateQty: function() {
            $(".qty").unbind().change(function(){
            	if($(this).attr('data-advanced-inventory')) {            		
            		var data = JSON.parse($(this).attr('data-advanced-inventory'));
            		var qty = 1;
            		if (data["validate-item-quantity"]["qtyIncrements"]) {
            			qty = data["validate-item-quantity"]["qtyIncrements"];
            		}                
            		$(this).val((Math.round($(this).val()/qty)*qty));
            	}
            });
        }

    });
 
    return $.qty.incdec;
});