define([
    'jquery',
    'cspModel',
    'cspViewStrategy'
], function ($, cspModel, strategy) {
    return function (config) {
        var model = cspModel(config);
        var type = config.type;
        var productMap = config.map;
        if (config['parent'] != null) {
            productMap['parent'] = config['parent'];
        }
        model.getPrices().done(function (response, textStatus) {
            strategy(type, response.data, productMap);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // In this case we don't want to update the prices
        });
    }
});
