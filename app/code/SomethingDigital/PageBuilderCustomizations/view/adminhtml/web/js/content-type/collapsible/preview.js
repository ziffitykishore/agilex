define([
    'jquery',
    'underscore',
    'knockout',
    'Magento_PageBuilder/js/events',
    'Magento_PageBuilder/js/content-type/preview-collection',
    'Magento_PageBuilder/js/content-type-factory',
    'Magento_PageBuilder/js/config',
    'matchMedia',
    'SomethingDigital_PageBuilderCustomizations/js/breakpoints',
    'mage/collapsible',
], function ($, _, ko, events, PreviewCollection, createContentType, pageBuilderConfig, mediaCheck, breakpoints) {
    'use strict';

    /**
     * @param parent
     * @param config
     * @param stageId
     * @constructor
     */
    function Preview(parent, config, stageId) {
        PreviewCollection.call(this, parent, config, stageId);
    }

    Preview.prototype = Object.create(PreviewCollection.prototype);

    /**
     * Root element
    //  */
    Preview.prototype.element = null;

    Preview.prototype.buildCollapsible = function buildCollapsible() {
        var _this5 = this,
            data = _this5.contentType.dataStore.getState();

        if (this.element) {
            try {
                $(_this5.element).collapsible('destroy');
            } catch (e) {};

            $(_this5.element).collapsible(_this5.buildCollapsibleConfig());

            if (data.default_active === 'true') {
                $(_this5.element).collapsible("activate");
            }

            mediaCheck({
                media: '(min-width: ' + (breakpoints.screen__m + 1) + 'px)',
                entry: function entry() {
                    if (data.only_mobile === 'true') {
                        $(_this5.element).collapsible("activate");
                        $(_this5.element).collapsible({collapsible: false});
                    }
                },
                exit: function exit() {
                    $(_this5.element).collapsible({collapsible: true});
                }
            });

            //Fix for inline editor not allowing space
            $(_this5.element).find(".coll-header span.coll-title").on('keydown', function (e) {
                console.log(e.keyCode);
                console.log(e.keyCode === 32);
                if(e.keyCode === 32) {
                    e.stopPropagation();
                }
            })
        }
    };

    Preview.prototype.buildCollapsibleConfig = function buildCollapsibleConfig() {
        var data = this.contentType.dataStore.getState();
        return {
            header: ".coll-header",
            active: data.default_active === "true",
            icons: {"header": "content-closed", "activeHeader": "content-opened"}
        };
    };

    /**
     * Bind events to add empty Collapsible item when Collapsible added and reinitialize Collapsible js when Collapsible item added
     */
    Preview.prototype.bindEvents = function bindEvents() {
        var self = this;
        PreviewCollection.prototype.bindEvents.call(this);

        events.on("collapsible:dropAfter", function (args) {
            if (args.id === self.contentType.id && self.contentType.children().length === 0) {
                self.addCollapsibleItem();
            }
        });

        events.on(this.config.name + ":renderAfter", function (args) {
            if (args.id === self.contentType.id) {
                self.element = args.element;
                self.buildCollapsible();
            }
        });

        events.on("form:" + this.contentType.id + ":saveAfter", function () {
            self.buildCollapsible();
        });
    };

    /**
     * Add Collapsible item
     */
    Preview.prototype.addCollapsibleItem = function () {
        var self = this;
        createContentType(
            pageBuilderConfig.getContentTypeConfig("collapsible-item"),
            this.contentType,
            this.contentType.stageId
        ).then(function (container) {
            self.contentType.addChild(container);
        });
    };

    return Preview;
});
