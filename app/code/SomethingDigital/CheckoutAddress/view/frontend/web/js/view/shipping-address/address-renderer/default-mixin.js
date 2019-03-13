define([
    'ko'
], function (ko) {
    'use strict';

    var mixin = {

        initObservable: function() {
            this._super();

            this.isVisible = ko.observable(false);

            var self = this;

            this.searchableAddress = ko.computed(function() {
                var address = self.address();
                var fields = [];

                fields.push(address.firstname + ' ' + address.lastname);
                if (address.company) {
                    fields.push(address.company);
                }
                address.street.forEach(function(street) { fields.push(street); });
                fields.push(address.city, address.region_id + ' ' + address.postcode);

                return fields.join(', ').toLowerCase();
            });

            if (self.address().isDefaultShipping()) {
                this.selectAddress();
            }

            return this;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});