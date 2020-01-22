define([
    'jquery',
    'cspSimple',
    'cspConfigurable',
    'cspGrouped',
    'cspBundle',
    'cspRelated',
    'cspUpsell',
    'cspCrosssell'
], function ($,simpleView, configurableView, groupedView, bundleView, relatedView, upsellView, crosssellView) {
    return function (type, data, currencySymbol, productMap, config) {
        if (type === 'simple') {
            simpleView(data, currencySymbol);
        } else if (type === 'configurable') {
            configurableView(data, currencySymbol, productMap);
        } else if (type === 'grouped') {
            groupedView(data, currencySymbol, productMap);
        } else if (type === 'bundle') {
            bundleView(data, currencySymbol);
        } else if (type === 'related') {
            relatedView(data, currencySymbol, config);
        } else if (type === 'upsell') {
            upsellView(data, currencySymbol, config);
        } else if (type === 'crosssell') {
            crosssellView(data, currencySymbol, config);
        }
    }
});
