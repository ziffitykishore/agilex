define([
    'jquery',
], function ($) {
    return function (data, currencySymbol, config, type) {
        var products = config[type + 'Products'];
        $('.products-' + type + ' .product-item').each(function(){
            var productId = $('.price-box',$(this)).data('product-id');
            var sku = products[productId];
            if (data[sku] !== undefined) {
                if (data[sku]['price'] !== 0 && data[sku]['price'] !== null) {
                    var price = data[sku]['price'];
                    var priceBoxId = "#product-price-" + productId;
                    var $el = $(priceBoxId + " .price");
                    $el.text(currencySymbol + price);
                }
            }
        });
    }
});
