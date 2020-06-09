define([], function () {
    "use strict";

    return function (target) {
        return target.extend({

            isFullMode: function () {
                if (!this.getTotals()) {
                    return false;
                }
                // Returns true to show price summary in checkout step one
                return true;
            }
        });
    }
});
