/*eslint-disable */

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; subClass.__proto__ = superClass; }

define(["jquery", "mage/translate", "Magento_PageBuilder/js/events", "underscore", "Magento_PageBuilder/js/config", "Magento_PageBuilder/js/content-type-menu/conditional-remove-option", "Magento_PageBuilder/js/content-type/preview-collection"], function (_jquery, _translate, _events, _underscore, _config, _conditionalRemoveOption, _previewCollection) {
  /**
   * Copyright © Magento, Inc. All rights reserved.
   * See COPYING.txt for license details.
   */

  /**
   * @api
   */
  var Preview =
  /*#__PURE__*/
  function (_preview2) {
    "use strict";

    _inheritsLoose(Preview, _preview2);

    function Preview() {
      var _this;

      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      _this = _preview2.call.apply(_preview2, [this].concat(args)) || this;
      _this.slideChanged = true;
      return _this;
    }

    var _proto = Preview.prototype;

    /**
     * Set state based on overlay mouseover event for the preview
     */
    ;

    _proto.onMouseOverWrapper = function onMouseOverWrapper() {
      // Triggers the visibility of the overlay content to show
      if (this.data.main.attributes()["data-show-overlay"] === "hover") {
        this.data.overlay.attributes(Object.assign(this.data.overlay.attributes(), {
          "data-background-color-orig": this.data.overlay.style().backgroundColor
        }));
        this.data.overlay.style(Object.assign(this.data.overlay.style(), {
          backgroundColor: this.data.overlay.attributes()["data-overlay-color"]
        }));
      }

      if (this.data.main.attributes()["data-show-button"] === "hover") {
        this.data.button.style(Object.assign(this.data.button.style(), {
          opacity: 1,
          visibility: "visible"
        }));
      }
    }

    /**
     * Set state based on overlay mouseout event for the preview
     */
    ;

    _proto.onMouseOutWrapper = function onMouseOutWrapper() {
      // Triggers the visibility of the overlay content to hide
      if (this.data.main.attributes()["data-show-overlay"] === "hover") {
        this.data.overlay.style(Object.assign(this.data.overlay.style(), {
          backgroundColor: this.data.overlay.attributes()["data-background-color-orig"]
        }));
      }

      if (this.data.main.attributes()["data-show-button"] === "hover") {
        this.data.button.style(Object.assign(this.data.button.style(), {
          opacity: 0,
          visibility: "hidden"
        }));
      }
    }

    /**
     * Get the options instance
     *
     * @returns {OptionsInterface}
     */
    ;

    _proto.retrieveOptions = function retrieveOptions() {
      var options = _preview2.prototype.retrieveOptions.call(this);

      delete options.move;
      options.remove = new _conditionalRemoveOption(_extends({}, options.remove.config, {
        preview: this
      }));
      return options;
    }

    /**
     * @inheritDoc
     */
    ;

    _proto.bindEvents = function bindEvents() {
      var _this5 = this;

      _preview2.prototype.bindEvents.call(this);

      _events.on(this.config.name + ":mountAfter", function (args) {
        if (args.id === _this5.contentType.id) {
          // Update the display label for the slide
          var slider = _this5.contentType.parentContentType;

          _this5.displayLabel((0, _translate)("Slide " + (slider.children().indexOf(_this5.contentType) + 1)));

          slider.children.subscribe(function (children) {
            var index = children.indexOf(_this5.contentType);

            _this5.displayLabel((0, _translate)("Slide " + (slider.children().indexOf(_this5.contentType) + 1)));
          });
        }
      });

      _events.on(this.config.name + ":renderAfter", function (args) {
        if (args.id === _this5.contentType.id) {
          var slider = _this5.contentType.parentContentType;
          (0, _jquery)(slider.preview.element).on("beforeChange", function () {
            _this5.slideChanged = false;
          });
          (0, _jquery)(slider.preview.element).on("afterChange", function () {
            _this5.slideChanged = true;
          });
        }
      });
    };

    return Preview;
  }(_previewCollection);

  return Preview;
});
//# sourceMappingURL=preview.js.map
