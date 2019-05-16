define([
    'cspSimple',
    'cspConfigurable',
    'cspGrouped',
    'cspBundle'
], function (simpleView, configurableView, groupedView, bundleView) {
    return function (type, data, currencySymbol, productMap) {
        if (type === 'simple') {
            simpleView(data, currencySymbol);
        } else if (type === 'configurable') {
            configurableView(data, currencySymbol, productMap);
        } else if (type === 'grouped') {
            groupedView(data, currencySymbol, productMap);
        } else if (type === 'bundle') {
            bundleView(data, currencySymbol);
        }
    }
});
