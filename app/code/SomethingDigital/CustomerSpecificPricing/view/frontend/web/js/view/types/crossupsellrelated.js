define([
    'jquery',
], function ($) {
    return function (data, currencySymbol, config, type) {
        var products = config[type + 'Products'];
        $('.products-' + type + ' .product-item').each(function(){
            let productId = $('.price-box',$(this)).data('product-id');
            let sku = products[productId];
            if (data[sku] !== undefined) {
                if (data[sku]['price'] !== 0 && data[sku]['price'] !== null) {
                    let price = data[sku]['price'];
                    let priceBoxId = "#product-price-" + productId;
                    let $el = $(priceBoxId + " .price");
                    if ($el.length > 0) {
                        $el.text(currencySymbol + price);
                    }
                }
            }
        });
    }
});
