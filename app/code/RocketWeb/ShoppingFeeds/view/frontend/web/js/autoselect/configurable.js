/*jshint browser:true jquery:true*/
define([
        "jquery",
        "jquery/ui",
        'jquery/jquery.parsequery'
    ],
    function($){
        "use strict";

        $.widget("rsf.autoSelectConfigurable", {
            values: {},
            _create: function() {
                var self = this;
                $('body').bind('onLoadedSwatches', function(e){
                    if ($('.swatch-opt').length) {
                        self._overrideDefaults();
                    }

                });
            },

            /**
             * Override default options values settings with either URL query parameters or
             * initialized inputs values.
             * @private
             */
            _overrideDefaults: function () {
                var hashIndex = window.location.href.indexOf('#');

                if (hashIndex !== -1) {
                    this._parseQueryParams(window.location.href.substr(hashIndex + 1));
                }

                this._selectValuesByAttribute();
            },
            /**
             * Parse query parameters from a query string and set options values based on the
             * key value pairs of the parameters.
             * @param {*} queryString - URL query string containing query parameters.
             * @private
             */
            _parseQueryParams: function (queryString) {
                var queryParams = $.parseQuery({
                    query: queryString
                });

                $.each(queryParams, $.proxy(function (key, value) {
                    this.values[key] = value;
                }, this));
            },

            /**
             * Select options with values based on each element's attribute identifier.
             * @private
             */
            _selectValuesByAttribute: function () {
                $.each(this.values, $.proxy(function (attributeId, optionId) {
                    if (!optionId) {
                        return;
                    }

                    var element = $("[attribute-id="+attributeId+"]");
                    var option = element.find("[option-id="+optionId+"]");

                    if (option.prop("tagName") == 'DIV') {
                        option.trigger('click');
                    }

                    if (option.prop("tagName") == 'OPTION') {
                        option.prop('selected', true);
                    }
                }, this));
            },
        });

        return $.feedForm;
    });