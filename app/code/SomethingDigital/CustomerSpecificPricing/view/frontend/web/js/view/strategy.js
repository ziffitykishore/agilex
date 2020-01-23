define([
    'jquery',
    'cspSimple',
    'cspConfigurable',
    'cspGrouped',
    'cspBundle',
    'cspCrossUpSellRelated'
], function ($,simpleView, configurableView, groupedView, bundleView, crossUpSellRelatedView) {
    return function (type, data, currencySymbol, productMap, config) {
        if (type === 'simple') {
            simpleView(data, currencySymbol);
        } else if (type === 'configurable') {
            configurableView(data, currencySymbol, productMap);
        } else if (type === 'grouped') {
            groupedView(data, currencySymbol, productMap);
        } else if (type === 'bundle') {
            bundleView(data, currencySymbol);
        } else if (type === 'related' || type === 'upsell' || type === 'crosssell') {
            crossUpSellRelatedView(data, currencySymbol, config, type);
        }
    }
});
