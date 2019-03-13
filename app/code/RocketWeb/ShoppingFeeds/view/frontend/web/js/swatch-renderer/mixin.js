define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {
            _init:function () {
                this._super();
                $('body').trigger('onLoadedSwatches');
            }
        });
        return $.mage.SwatchRenderer;
    }
});