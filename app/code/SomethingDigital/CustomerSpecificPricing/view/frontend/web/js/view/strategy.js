define([
    'cspSimple',
    'cspConfigurable',
    'cspGrouped',
    'cspBundle'
], function (simpleView, configurableView, groupedView, bundleView) {
    return function (type, data, productMap) {
        if (type === 'simple') {
            simpleView(data);
        } else if (type === 'configurable') {
            configurableView(data, productMap);
        } else if (type === 'grouped') {
            groupedView(data, productMap);
        } else if (type === 'bundle') {
            bundleView(data);
        }
    }
});
