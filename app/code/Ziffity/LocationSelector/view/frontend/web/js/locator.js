define([
	'jquery',
	'ko',
	'uiComponent',
        'mage/cookies',
	'Magento_Ui/js/modal/modal'
], function($,ko,Component,modal) {
 	'use strict';
    
    return Component.extend({
        
        storeLocations : ko.observable(""),
        
        initialize: function () {
            this._super();
            
            if($.cookie('storeLocation')){
                this.storeLocations($.cookie('storeLocation'));
            }else{
                this.getLocation();
            }
        },
        getLocation: function(){
            var obj;
            obj = this;
            $.ajax({
                showLoader: true,
                url: window.location.origin+'/locator/index/index',
                type: 'GET',
                dataType: 'json'
            }).done(function (data) {
                obj.storeLocations(data[0]);
                $.cookie('storeLocation',data[0]);
            });            
        }
    });
});