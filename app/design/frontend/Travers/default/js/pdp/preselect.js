import $ from 'jquery';
import domready from 'domready';

/*
    This Javascript is to Pre-Select Configurable Attributes on Page Load

    It looks for <div class="swatch-auto-selector" data-color="Red"></div> where data-* are the attribute
    values to select on page-load.
*/

let galleryLoaded = false;
let swatchInitialized = false;

const preselectAttributes = function(element) {
    // Has BOTH gallery & swatch JS run?
    if (!galleryLoaded || !swatchInitialized) {
        return false;
    }
    // If there's only 1 swatch available, let's pre-select
    $('.product-add-form')
        .find('.swatch-attribute-options')
        .each(function(index, element) {
        if ($(element).find('.swatch-option').length == 1) {
            $(element)
            .find('.swatch-option')
            .first()
            .click();
        }
        });

    // Check if configurable has pre-selected attributes & click them
    $.each($('.swatch-auto-selector').data(), (key, value) => {
        $(`.swatch-attribute[attribute-code="${key}"] .swatch-attribute-options .swatch-option[option-label="${value}"]`).click();
    });
};

domready(() => {
    // check if swatch-auto-selector div exists (means this is a configurable product)
    if ($('.swatch-auto-selector').length) {
        // Event fires when PDP gallery has finished loading
        $(document).one('gallery:loaded', (event) => {
            galleryLoaded = true;
            preselectAttributes();
        });

        // Event fires when all swatch options are built on frontend PDP
        $(document).one('swatch.initialized', (event) => {
            swatchInitialized = true;
            preselectAttributes();
        });
    }
});