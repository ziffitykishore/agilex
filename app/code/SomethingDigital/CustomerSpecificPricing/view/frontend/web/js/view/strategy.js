define([
    'cspSimple',
    'cspConfigurable',
    'cspGrouped'
], function (simpleView, configurableView, groupedView) {
    return function (type, data, productMap) {
        if (type === 'simple') {
            simpleView(data);
        } else if (type === 'configurable') {
            configurableView(data, productMap);
        } else if (type === 'grouped') {
            groupedView(data, productMap);
        }
    }
});
