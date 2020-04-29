define([
    'jquery',
], function ($) {
    return function (suffix) {
        var sku = $('[itemprop=sku]').text();
        if (suffix !== null) {
            $('[itemprop=sku]').text(sku + suffix);
        }
    }
});