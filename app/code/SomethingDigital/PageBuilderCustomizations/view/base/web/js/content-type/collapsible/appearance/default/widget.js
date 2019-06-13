define([
    'jquery',
    'matchMedia',
    'SomethingDigital_PageBuilderCustomizations/js/breakpoints',
    'collapsible'
], function ($,mediaCheck, breakpoints) {
    'use strict';

    return function (config, element) {
        var element = $(element);

        if (element) {
            $(element).collapsible({
                active: element.data('activeDefault'),
                icons: {"header": "content-closed", "activeHeader": "content-opened"}
            });

            if (element.data('activeDefault')) {
                $(element).collapsible("activate");
            }

            mediaCheck({
                media: '(min-width: ' + (breakpoints.screen__m + 1) + 'px)',
                entry: function entry() {
                    if (element.data('onlyMobile')) {
                        $(element).collapsible("activate");
                        $(element).collapsible({collapsible: false});
                    }
                },
                exit: function exit() {
                    $(element).collapsible({collapsible: true});
                }
            });
        }
    };
});
