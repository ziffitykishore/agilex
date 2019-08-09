/*eslint-disable */

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; subClass.__proto__ = superClass; }

define(["Magento_PageBuilder/js/mass-converter/widget-directive-abstract", "Magento_PageBuilder/js/utils/object"], function (_widgetDirectiveAbstract, _object) {
  /**
   * @api
   */
  var WidgetDirective =
      /*#__PURE__*/
      function (_widgetDirectiveAbstr) {
        "use strict";

        _inheritsLoose(WidgetDirective, _widgetDirectiveAbstr);

        function WidgetDirective() {
          return _widgetDirectiveAbstr.apply(this, arguments) || this;
        }

        var _proto = WidgetDirective.prototype;

        /**
         * Convert value to internal format
         *
         * @param {object} data
         * @param {object} config
         * @returns {object}
         */
        _proto.fromDom = function fromDom(data, config) {
          var attributes = _widgetDirectiveAbstr.prototype.fromDom.call(this, data, config);

          data.parent_category_id = attributes.parent_category_id;
          return data;
        }
        /**
         * Convert value to knockout format
         *
         * @param {object} data
         * @param {object} config
         * @returns {object}
         */
        ;

        _proto.toDom = function toDom(data, config) {
          var attributes = {
            type: "SomethingDigital\\CategoryWidget\\Block\\Category\\CategoriesList",
            template: "SomethingDigital_CategoryWidget::category/widget/content/list.phtml",
            anchor_text: "",
            id_path: "",
            show_pager: 0,
            parent_category_id: data.parent_category_id,
            type_name: "Catalog Categories List",
          };

          (0, _object.set)(data, config.html_variable, this.buildDirective(attributes));
          return data;
        }
        /**
         * @param {string} content
         * @returns {string}
         */
        ;

        return WidgetDirective;
      }(_widgetDirectiveAbstract);

  return WidgetDirective;
});
//# sourceMappingURL=widget-directive.js.map
