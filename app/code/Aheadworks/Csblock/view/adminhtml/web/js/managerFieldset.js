/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    "jquery",
    "loadingPopup"
], function($){
    'use strict';

    $.awCsblockManagerFieldset = {
        addDependence: function(dependentSelector, headSelector, values)
        {
            jQuery(headSelector).change(function(){
                if (values.indexOf(this.value) < 0) {
                    jQuery(dependentSelector).hide();
                } else {
                    jQuery(dependentSelector).show();
                }
            });

            jQuery(document).ready(function(){
                if (values.indexOf(jQuery(headSelector).val()) < 0) {
                    jQuery(dependentSelector).hide();
                } else {
                    jQuery(dependentSelector).show();
                }
            });
        }
    };

    return $.awCsblockManagerFieldset;
});