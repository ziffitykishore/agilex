/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'rjsResolver',
    'jquery'
], function (resolver,$) {
    'use strict';

    /**
     * Removes provided loader element from DOM.
     *
     * @param {HTMLElement} $loader - Loader DOM element.
     */
    function hideLoader($loader) {
        $loader.parentNode.removeChild($loader);
        var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
        if (isChrome) {
            $("input:-webkit-autofill").each(function () {
                $(this).closest('.field').addClass('focused');
            });
        }
        var street = $('[name="street[0]"');
        street.closest('fieldset').find('legend').remove();
        street.closest('fieldset').find('label').text('Street Address');
        $('#co-shipping-form .input-text').each(function() {
            if($(this).val().trim() !== '') {
                $(this).closest('.field').addClass('focused');
            } 
        });
       
    }

    /**
     * Initializes assets loading process listener.
     *
     * @param {Object} config - Optional configuration
     * @param {HTMLElement} $loader - Loader DOM element.
     */
    function init(config, $loader) {
        resolver(hideLoader.bind(null, $loader));
    }

    return init;
});
