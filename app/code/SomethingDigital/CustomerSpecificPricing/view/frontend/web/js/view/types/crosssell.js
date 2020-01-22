define([
    'jquery',
], function ($) {
    return function (data, currencySymbol, config) {
        var products = config.crosssellProducts;
        $('.products-crosssell .product-item').each(function(){
            let productId = $('.price-box',$(this)).data('product-id');
            let sku = products[productId];
            if (data[sku] !== undefined) {
                if (data[sku]['price'] != 0 && data[sku]['price'] != null) {
                    var price = data[sku]['price'];
                    var priceBoxId = "#product-price-" + productId;
                    $el = $(priceBoxId + " .price");
                    if ($el.length > 0) {
                        $el.text(currencySymbol + price);
                    }
                }
            }
        });
    }
});
