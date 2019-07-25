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
            if($.cookie('storeLocation') && $.cookie('stores')){
                var currentStore = JSON.parse($.cookie('storeLocation'));
                this.storeLocations(currentStore.name);
                var storeList = JSON.parse($.cookie('stores'));
                this.stores(storeList);
            }else{
                this.getLocation();
            }
        },
        getLocation: function(){
            var obj = this;
            $.ajax({
                showLoader: false,
                url: window.location.origin+'/locator/index/index',
                type: 'GET',
                dataType: 'json'
            }).done(function (data) {
                obj.storeLocations(data[0]['name']);
                obj.stores(data);
                var date = new Date();
                date.setTime(date.getTime() + 3 * (60 * (60 * 1000)));
                $.cookie(
                    'storeLocation',
                    JSON.stringify({code : data[0]['code'], name : data[0]['name']}),
                    {expires : date}
                );
                $.cookie(
                    'stores',
                    JSON.stringify(data),
                    {expires : date}
                );
            });
        },
        selectionChanged: function() {
            var date = new Date();
            date.setTime(date.getTime() + 3 * (60 * (60 * 1000)));
            $.cookie(
                'storeLocation',
                JSON.stringify({code : $('#location-selector option:selected').val(), name : $('#location-selector option:selected').text()}),
                {expires : date}
            );
            location.reload(true);
        },
        currentLocation : function() {
            var location = JSON.parse($.cookie('storeLocation'));
            if(location){
                return location.code;
            }
            
        }
    });
});