define([
    'jquery',
    'cspModel',
    'cspViewStrategy'
], function ($, cspModel, strategy) {
    return function (config) {
        var model = cspModel(config);
        var type = config.type;
        var currencySymbol = config.currencySymbol;
        var productMap = config.map;
        if (config['parent'] != null) {
            productMap['parent'] = config['parent'];
        }

        if (type == 'crosssell') {
            model.getPrices('crosssell').done(function (response, textStatus) {
                strategy('crosssell', response.data, currencySymbol, productMap, config);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // In this case we don't want to update the prices
            });
        } else {
            model.getPrices(false).done(function (response, textStatus) {
                strategy(type, response.data, currencySymbol, productMap, config);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // In this case we don't want to update the prices
            });

            model.getPrices('related').done(function (response, textStatus) {
                strategy('related', response.data, currencySymbol, productMap, config);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // In this case we don't want to update the prices
            });

            model.getPrices('upsell').done(function (response, textStatus) {
                strategy('upsell', response.data, currencySymbol, productMap, config);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // In this case we don't want to update the prices
            });
        }
    }
});
