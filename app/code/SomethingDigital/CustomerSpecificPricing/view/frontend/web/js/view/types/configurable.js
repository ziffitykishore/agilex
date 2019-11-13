define([
    'jquery',
], function ($) {
    return function (data, currencySymbol, map) {

        // Updates default price on configurable PDP when no 
        // swatches are selected
        updateParentProductPrice();

        $('.price-box.price-final_price').on('updatePrice', function(e, info) {
            var simpleProductId = info['id'];
            var sku = map[simpleProductId];
            if (is_array(data[sku]) && isset(data[sku]['price']) && data[sku]['price'] != 0 && data[sku]['price'] != null) {
                $('.product-info-main .price').text(currencySymbol + data[sku]['price']);
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
            if (sku == null || data[sku]['price'] == null) {
                return;
            }
            if (is_array(data[sku]) && isset(data[sku]['price']) && data[sku]['price'] != 0) {
                $('.product-info-main .price').text(currencySymbol + data[sku]['price']);
            }
        }
    }
});
