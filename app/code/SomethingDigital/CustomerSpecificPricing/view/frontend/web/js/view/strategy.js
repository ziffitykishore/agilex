define([
    'cspSimple',
    'cspConfigurable'
], function (simpleView, configurableView) {
    return function (type, data, productMap) {
        if (type === 'simple') {
            simpleView(data);
        } else if (type === 'configurable') {
            configurableView(data, productMap);
        }
    }
});
