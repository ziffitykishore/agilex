define([
    'cspSimple'
], function (simpleView) {
    return function (type, data) {
        if (type === 'simple') {
            simpleView(data);
        }
    }
});
