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
                var data = JSON.parse($(this).siblings('input').attr('data-advanced-inventory'));
                var qty = 1;
                if (data["validate-item-quantity"]["qtyIncrements"]) {
                    qty = data["validate-item-quantity"]["qtyIncrements"];
                }
                $(this).siblings('input').val(
                    parseInt($(this).siblings('input').val()) + qty
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
                } else {
                    $(this).next().val(0);
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
                    if($(this).val() < 0) {
                       $(this).val(0);
                    } else {
                        $(this).val((Math.round($(this).val()/qty)*qty));
                    }
            	}
            });
        }

    });
 
    return $.qty.incdec;
});