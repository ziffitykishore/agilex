define([
    'jquery',
], function ($) {
    return function (data, map) {

        // Updates default price on configurable PDP when no 
        // swatches are selected
        updateParentProductPrice();

        $('.price-box.price-final_price').on('updatePrice', function(e, info) {
            var simpleProductId = info['id'];
            var sku = map[simpleProductId];
            if (data[sku] != null) {
                if (data[sku] != 0) {
                    $('.product-info-main .price').text('$' + data[sku]);
                }
            } else {
                // if the user deslected a swatch we'll need to fall
                // back to the configurable product
                updateParentProductPrice();
            }
        });

        function updateParentProductPrice() {
            var parentId = map['parent'];
            var sku = map[parentId];
             
            // Skip update if we don't have a nourison sku or customer
            // specific price
            if (sku == null || data[sku] == null) {
                return;
            }
            if (data[sku] != 0) {
                $('.product-info-main .price').text('$' + data[sku]);
            }
        }
    }
});
