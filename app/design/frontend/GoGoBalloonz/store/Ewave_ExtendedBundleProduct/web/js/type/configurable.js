/* global Option */
define([
    'jquery',
    'underscore',
    'mage/template',
    'Magento_Catalog/js/price-utils',
    'jquery/ui',
    'priceBundle',
    'mage/validation'
], function ($, _, mageTemplate, utils) {
    'use strict';
    var gallerySelector = '[data-gallery-role=gallery-placeholder]';
    var gallery,galleryImages,spConfig;
    $.widget('mage.bundleConfigurable', {
        options: {
            superSelector: '.super-attribute-select',
            selectSimpleProduct: '[name="bundle_selected_configurable_option"]',
            priceHolderSelector: '#bundleSummary .price-box',
            state: {},
            spConfig: {},
            priceTemplate: '<span class="price"><%- data.formatted %></span>',
            productAddToForm: '#product_addtocart_form',
            bundleItem: '.bundle-item',
            bundleOption: null,
            selectionId: null,
            configurableOptions: {}
        },

        _create: function () {
            // Initial setting of various option values
            this._initializeOptions();

            // Change events to check select reloads
            this._setupChangeEvents();

            // Fill state
            this._fillState();

            // Setup child and prev/next settings
            this._setChildSettings();

            // Setup/configure values to inputs
            this._configureForValues();
        },

        /**
         * Initialize tax configuration, initial settings, and options values.
         * @private
         */
        _initializeOptions: function () {
            this.options.settings = (this.options.containerId) ? $(this.options.containerId).find(this.options.superSelector) : $(this.options.superSelector);

            this.options.values = this.options.spConfig.defaultValues || {};
            this.inputSimpleProduct = this.element.find(this.options.selectSimpleProduct);
            if (this.options.inputType === 'select') {
                this.bundleField = $('#bundle-option-' + this.options.bundleOption + ' option[value="' + this.options.selectionId + '"]');
            } else {
                this.bundleField = $('#bundle-option-' + this.options.bundleOption + '-' + this.options.selectionId);
            }
            this.lockedProducts = this.getLockedProducts();
        },

        /**
         * Set up .on('change') events for each option element to configure the option.
         * @private
         */
        _setupChangeEvents: function () {
            $.each(this.options.settings, $.proxy(function (index, element) {
                $(element).on('change', this, this._configure);
            }, this));
        },

        /**
         * Iterate through the option settings and set each option's element configuration,
         * attribute identifier. Set the state based on the attribute identifier.
         * @private
         */
        _fillState: function () {
            $.each(this.options.settings, $.proxy(function (index, element) {
                var attributeId = element.id.replace(/[a-z]*/, '').match(/[^-]*/g)[0];
                spConfig = this.options.spConfig;
                if (attributeId && this.options.spConfig.attributes[attributeId]) {
                    element.config = this.options.spConfig.attributes[attributeId];
                    this.removeLockedProducts(element.config);
                    element.attributeId = attributeId;
                    this.options.state[attributeId] = false;
                }
            }, this));
        },

        /**
         * Set each option's child settings, and next/prev option setting. Fill (initialize)
         * an option's list of selections as needed or disable an option's setting.
         * @private
         */
        _setChildSettings: function () {
            var childSettings = [],
                settings = this.options.settings,
                index = settings.length,
                option;

            while (index--) {
                option = settings[index];

                !index ? this._fillSelect(option) : (option.disabled = true);

                _.extend(option, {
                    childSettings: childSettings.slice(),
                    prevSetting: settings[index - 1],
                    nextSetting: settings[index + 1]
                });

                childSettings.push(option);
            }
        },

        /**
         * Setup for all configurable option settings. Set the value of the option and configure
         * the option, which sets its state, and initializes the option's choices, etc.
         * @private
         */
        _configureForValues: function () {
            if (this.options.values) {
                this.options.settings.each($.proxy(function (index, element) {
                    var attributeId = element.attributeId;
                    element.value = (typeof (this.options.values[attributeId]) === 'undefined') ? '' : this.options.values[attributeId];
                    this._configureElement(element);
                }, this));
            }
        },

        /**
         * Event handler for configuring an option.
         * @private
         * @param event Event triggered to configure an option.
         */
        _configure: function (event) {
            event.data._configureElement(this);
            event.data._reloadPrice(this);
            if(!galleryImages) {
                gallery = $(gallerySelector).data('gallery');
                galleryImages = gallery.returnCurrentImages();
            }
            var selectClass = '.' + $(this).attr('class');
            var selectedIds = [];
            var length = $(selectClass).length;
            var count = 0;
            $(selectClass).each(function(){
               if($(this).val()){
                   var selected = $(this).children('option:selected');
                   var productId = $(selected).attr('data-product-ids');
                   selectedIds.push(productId.split(','));
                   count++;
               }
            });
            if (length === count) { // last configurable option
                var mergeSelectedIds = [];
                for (var i in selectedIds) {
                    for(var j in selectedIds[i]) {
                        mergeSelectedIds.push(selectedIds[i][j]); // merge both array in selected items from dropdown
                    }
                }
                var id = '';
                if(mergeSelectedIds.length > 1) { // find product id by finding first duplicate element in array;
                    var isMatch = false;
                    for(var i=0; i<mergeSelectedIds.length;i++) {
                        for (var j = i+1; j < mergeSelectedIds.length; j++) {
                            if (mergeSelectedIds[i] === mergeSelectedIds[j]){
                                id = mergeSelectedIds[i];
                                isMatch=true;
                                break;
                           }
                        }
                        if (isMatch) break;
                    }
                }else{
                    id = mergeSelectedIds[0];
                }
                var imgs = spConfig.images[id];
                var newImages = [];
                for(var i in imgs) {
                    var srcImg = imgs[i].img;
                    var srcThumb = imgs[i].thumb;
                    var srcFull = imgs[i].full; 
                    newImages.push({
                        img:     srcImg,
                        thumb: srcThumb,
                        full:       srcFull,
                        caption: 'image',
                        position: 1
                    });
                }
                for (var i=0;i<galleryImages.length;i++) {
                         newImages.push({
                           img: galleryImages[i].img,
                           thumb: galleryImages[i].thumb,
                           full:       galleryImages[i].full,
                           caption: galleryImages[i].caption,
                           position: i+2
                        });
                    }
                    gallery.updateData(newImages);
            }
            else{ // set default images
               if(gallery && galleryImages) {
                    gallery.updateData(galleryImages);
               }
           }
        },

        /**
         * Configure an option, initializing it's state and enabling related options, which
         * populates the related option's selection and resets child option selections.
         * @private
         * @param element The element associated with a configurable option.
         */
        _configureElement: function (element) {
            this.simpleProduct = this._getSimpleProductId(element);

            if (element.value) {
                this.options.state[element.config.id] = element.value;
                if (element.nextSetting) {
                    element.nextSetting.disabled = false;
                    this._fillSelect(element.nextSetting);
                    this._resetChildren(element.nextSetting);
                } else {
                    this.inputSimpleProduct.val(element.selectedOptions[0].config.allowedProducts[0]);
                }
            } else {
                this._resetChildren(element);
            }
        },

        /**
         * For a given option element, reset all of its selectable options. Clear any selected
         * index, disable the option choice, and reset the option's state if necessary.
         * @private
         * @param element The element associated with a configurable option.
         */
        _resetChildren: function (element) {
            if (element.childSettings) {
                for (var i = 0; i < element.childSettings.length; i++) {
                    element.childSettings[i].selectedIndex = 0;
                    element.childSettings[i].disabled = true;
                    if (element.config) {
                        this.options.state[element.config.id] = false;
                    }
                }
            }
        },

        /**
         * Populates an option's selectable choices.
         * @private
         * @param element Element associated with a configurable option.
         */
        _fillSelect: function (element) {
            var attributeId = element.id.replace(/[a-z]*/, '').match(/[^-]*/g)[0],
                options = this._getAttributeOptions(attributeId),
                prevConfig,
                index,
                i,
                allowedProducts,
                j;
            this._clearSelect(element);
            element.options[0] = new Option('', '');
            element.options[0].innerHTML = this.options.spConfig.chooseText;
            var attributes = this.options.spConfig.attributes;
            var productIds = {};
            for (var attr in this.options.spConfig.attributes) {
                 for(var i in attributes[attr].options) { 
                   productIds[attributes[attr].options[i].id] = attributes[attr].options[i].products;
                }
            }
            prevConfig = false;
            if (element.prevSetting) {
                prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
            }
            if (options) {
                index = 1;
                for (i = 0; i < options.length; i++) {
                    allowedProducts = [];
                    if (prevConfig) {
                        for (j = 0; j < options[i].products.length; j++) {
                            // prevConfig.config can be undefined
                            if (prevConfig.config &&
                                prevConfig.config.allowedProducts &&
                                prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                allowedProducts.push(options[i].products[j]);
                            }
                        }
                    } else {
                        allowedProducts = options[i].products.slice(0);
                    }
                    if (allowedProducts.length > 0) {
                        options[i].allowedProducts = allowedProducts;
                        element.options[index] = new Option(this._getOptionLabel(options[i]), options[i].id);
                        if (typeof options[i].price !== 'undefined') {
                            element.options[index].setAttribute('price', options[i].prices);
                        }
                        element.options[index].config = options[i];
                        index++;
                    }
                }

                if (this.isRequireItem()) {
                    this.setOptionValidation();
                }
            }
            for(var i=1;i<=options.length;i++) {
                 element.options[i].setAttribute('data-product-ids',productIds[options[i-1].id]);
            }
        },

        /**
         * Generate the label associated with a configurable option. This includes the option's
         * label or value and the option's price.
         * @private
         * @param option A single choice among a group of choices for a configurable option.
         * @param selOption Current selected option.
         * @return {String} The option label with option value and price (e.g. Black +1.99)
         */
        _getOptionLabel: function (option, selOption) {
            return option.label;
        },

        /**
         * Removes an option's selections.
         * @private
         * @param element The element associated with a configurable option.
         */
        _clearSelect: function (element) {
            for (var i = element.options.length - 1; i >= 0; i--) {
                element.remove(i);
            }
        },

        /**
         * Retrieve the attribute options associated with a specific attribute Id.
         * @private
         * @param attributeId The id of the attribute whose configurable options are sought.
         * @return {Object} Object containing the attribute options.
         */
        _getAttributeOptions: function (attributeId) {
            if (this.options.spConfig.attributes[attributeId]) {
                return this.options.spConfig.attributes[attributeId].options;
            }
        },

        /**
         * Is required configurable item of bundle product
         * @returns {*}
         */
        isRequireItem: function () {
            if (this.options.inputType === 'select') {
                return this.bundleField.is(':selected');
            }
            return this.bundleField.is(':checked');
        },

        /**
         * Set option validation after fill select
         */
        setOptionValidation: function () {
            if ($(this.options.productAddToForm).data('mageValidation') !== undefined) {
                $(this.element).find(this.options.superSelector).each(function () {
                    $(this).rules('add', { 'required': true });
                });
            }
        },

        /**
         * Reload the price of the configurable item incorporating the prices of all of the
         * configurable product's option selections.
         */
        _reloadPrice: function (element) {
            var prices,
                optionConfig = $(this.options.productAddToForm).priceBundle('option', 'optionConfig'),
                optionSelections,
                priceFormat,
                priceTemplate,
                bundleItemPrice,
                $priceHolder;

            if (optionConfig.isFixedPrice) {
                return;
            }
            prices = this._getPrices();
            optionSelections = optionConfig.options[this.options.bundleOption].selections;
            priceFormat = $(this.options.productAddToForm).priceBundle('option', 'priceFormat') || {};
            priceTemplate = mageTemplate(this.options.priceTemplate);
            bundleItemPrice = {};
            $priceHolder = $('[data-price-type]', this.bundleField.closest(this.options.bundleItem));

            if (!_.isEmpty(prices)) {
                bundleItemPrice.formatted = utils.formatPrice(prices.finalPrice.amount, priceFormat);
                optionSelections[this.options.selectionId].prices = prices;
                if (this.options.inputType === 'select') {
                    this.bundleField.closest('select').trigger('change');
                } else {
                    this.bundleField.trigger('change');
                }
            } else {
                bundleItemPrice.formatted = utils.formatPrice($priceHolder.data('priceAmount'), priceFormat);
            }

            $priceHolder.html(priceTemplate({
                data: bundleItemPrice
            }));
        },

        /**
         * Get product various prices
         * @returns {{}}
         * @private
         */
        _getPrices: function () {
            var prices = {},
                elements = _.toArray(this.options.settings),
                hasProductPrice = false;

            _.each(elements, function (element) {
                var selected = element.options[element.selectedIndex],
                    config = selected && selected.config,
                    priceValue = {};

                if (config && config.allowedProducts.length === 1 && !hasProductPrice) {
                    priceValue = this._calculatePrice(config);
                    hasProductPrice = true;
                }

                if (!_.isEmpty(priceValue)) {
                    prices = priceValue;
                }
            }, this);

            return prices;
        },

        /**
         * Returns prices for configured products
         *
         * @param {*} config - Products configuration
         * @returns {*}
         * @private
         */
        _calculatePrice: function (config) {
            var displayPrices = $(this.options.priceHolderSelector).priceBox('option').prices,
                newPrices = this.options.spConfig.optionPrices[_.first(config.allowedProducts)];

            _.each(displayPrices, function (price, code) {
                if (newPrices[code]) {
                    displayPrices[code].amount = newPrices[code].amount - displayPrices[code].amount;
                }
            });

            return displayPrices;
        },

        /**
         * Returns Simple product Id
         *  depending on current selected option.
         *
         * @private
         * @param {HTMLElement} element
         * @returns {String|undefined}
         */
        _getSimpleProductId: function (element) {
            var allOptions = element.config.options,
                value = element.value,
                config;

            if (element.prevSetting && !element.value) {
                value = allOptions[0].id;
            }

            config = _.filter(allOptions, function (option) {
                return option.id === value;
            });
            config = _.first(config);

            return _.isEmpty(config) ? undefined : _.first(config.allowedProducts);
        },
        /**
         * Returns locked products
         * @returns {Array}
         */
        getLockedProducts: function () {
            var lock = [];
            _.each(this.options.configurableOptions, function (value, key) {
                if (!value.selected) {
                    lock.push(value.value);
                }
            });
            return lock;
        },
        /**
         * Removes locked products from original configuration
         * @param config
         */
        removeLockedProducts: function (config) {
            var self = this;
            if (this.lockedProducts.length) {
                _.each(config.options, function (option) {
                    option.products = _.difference(option.products, self.lockedProducts);
                });
            }
        }
    });
    return $.mage.bundleConfigurable;
});
