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
        stores : ko.observableArray(),
        
        initialize: function () {
            this._super();
            var stores = JSON.parse($.cookie('stores'));
            if($.cookie('storeLocation') && $.cookie('stores')){
                this.storeLocations($.cookie('storeLocation'));
                this.stores(stores);
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
                var result = Object.keys(data).map(function(key) { // convert object to array
                  return [data[key]];
                });
                obj.stores(result);
                $.cookie('storeLocation',data[0]);
                $.cookie('stores',JSON.stringify(result));
            });            
        },
        selectionChanged: function() {
            $.cookie('storeLocation',this.storeLocations());
            window.location.reload();
        }
    });
});