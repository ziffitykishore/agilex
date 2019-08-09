define([], function () {
    'use strict';    

    return function (List) {
        return List.extend({
            initConfig: function () {
                this._super();
                this.addressOptions.pop();
                return this;
            }
        });
    }
});