define([
    'jquery',
    'underscore'
], function ($, underscore) {
    'use strict';

    return function (config, element) {
        var $element = $(element);

        if($element.data('fullbleed')) {
            updateMaxWidth($element);
            $(window).on('resize', underscore.debounce(updateMaxWidth.bind(null, $element), 1000 / 60));
        }
    };

    function updateMaxWidth(element) {
        const browserWidthMinusScrollbars = $('body').innerWidth();
        const maxWidthString = $('.page-main').css('max-width');
        const fullbleedClasses = $(element).find('.pagebuilder-wrapper.fullbleed');

        if (!maxWidthString) {
            // reset values
            $(fullbleedClasses).css({
                'max-width': '',
                'margin-left': '',
                'margin-right': '',
            });

            // exit function
            return false;
        }

        const maxWidth = parseInt(maxWidthString.replace('px', ''));

        const paddingLeft = $('.page-main').css('padding-left');
        if (typeof paddingLeft != 'undefined') {
            const containerPadding = parseInt(paddingLeft.replace('px', ''));

            let marginLeft = 0;
            let marginRight = 0;

            if (browserWidthMinusScrollbars >= maxWidth) {
                marginLeft = -((browserWidthMinusScrollbars - maxWidth + (containerPadding * 2)) / 2);
                marginRight = -((browserWidthMinusScrollbars - maxWidth + (containerPadding * 2)) / 2);
            } else {
                marginLeft = -(containerPadding);
                marginRight = -(containerPadding);
            }

            $(fullbleedClasses).css({
                'max-width': browserWidthMinusScrollbars,
                'margin-left': marginLeft,
                'margin-right': marginRight,
            });
        }
    }
});
