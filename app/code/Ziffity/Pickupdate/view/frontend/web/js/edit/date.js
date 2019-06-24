define([
        'jquery',
        'Ziffity_Pickupdate/js/checkout/date'
	], function(
		$,
        checkoutDate
	){
		'use strict';
		return  checkoutDate.extend({

            defaults  : {
                options : {}
            },
            initConfig: function (options) {
                // drown UI functional
                this.pickupconf = options.pickupconf;
            },
            initObservable: function() {
                // drown UI functional
                return this;
            },
            setInitialValue: function() {
                // drown UI functional
                return this;
            },
            initSwitcher: function() {
                // drown UI functional
                return this;
            }
        });
	}
);