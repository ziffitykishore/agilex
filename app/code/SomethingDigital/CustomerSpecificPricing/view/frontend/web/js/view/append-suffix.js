define([
    'jquery',
], function ($) {
    return function () {
        var settings = {
            method: 'POST',
            dataType: 'json',
            url: '/csp/skusuffix'
        }

        $.ajax(settings).done(function(data){
            var sku = $('[itemprop=sku]').text();
            if (data.suffix !== null) {
                $('[itemprop=sku]').text(sku + data.suffix);
            }
        });
    }
});
