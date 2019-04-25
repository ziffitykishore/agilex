define([
    'jquery',
    'cspModel',
    'cspViewStrategy'
], function ($, cspModel, strategy) {
    return function (config) {
        var model = cspModel(config);
        var type = config.type;

        model.getPrices().done(function (response, textStatus) {
            strategy(type, response.data);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // In this case we don't want to update the prices
        });
    }
});
