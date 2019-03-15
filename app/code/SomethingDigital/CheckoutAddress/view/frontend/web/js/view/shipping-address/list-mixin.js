define([
    'ko'
], function (ko) {
    'use strict';

    var renderedCount = 0;

    var mixin = {

        searchInput: ko.observable(''),

        /**
         * Displays the first 5 elements plus the active(default) element during
         * initial rendering.
         *
         * @param elements
         * @param data
         */
        renderHandler: function (elements, data) {
            if (renderedCount < 5 || data.isSelected()) {
                data.isVisible(true);
            }
            renderedCount++;
        },

        displayResults: function (query) {
            var matches = 0;

            if (query === undefined || query === '') {
                this.resetResults();
                return;
            }

            this.elems().forEach(function (elem) {
                if (elem.searchableAddress().indexOf(query.toLowerCase()) > -1 && matches < 10) {
                    elem.isVisible(true);
                    matches++;
                } else {
                    elem.isVisible(false);
                }
            });
        },

        resetResults: function () {
            var displayed = 0;

            this.elems().forEach(function (elem) {
                if (displayed < 5 || elem.isSelected()) {
                    elem.isVisible(true);
                    displayed++;
                } else {
                    elem.isVisible(false);
                }
            });
        },

        initialize: function () {
            this._super();

            var self = this;

            this.searchInput.subscribe(function (changes) {
                self.displayResults(changes);
            });

            return this;
        },

        /**
         * Create new component that will render given address in the address list
         * Only Renders Addresses with `shipping_site_id` or if address was created
         * by customer during checkout (because customers can only create new
         * shipping addresses).
         *
         * @param address
         * @param index
         */
        createRendererComponent: function (address, index) {
            try {
                if (address.getType() === 'new-customer-address'
                    || address.customAttributes.shipping_site_id
                ) {
                    this._super(address, index);
                }
            } catch (e) { }
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});