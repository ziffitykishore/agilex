define([
    'jquery',
    'jquery/ui',
    'MagicToolbox_MagicZoomPlus/js/configurable'
], function($){

    $.widget('silvan.configurable', $.magictoolbox.configurable, {
        /**
         * Configure an option, initializing it's state and enabling related options, which
         * populates the related option's selection and resets child option selections.
         * @private
         * @param {*} element - The element associated with a configurable option.
         */
        _configureElement: function (element) {
            this.simpleProduct = this._getSimpleProductId(element)
            $(".productAlert").hide();
            if (element.value) {
                this.options.state[element.config.id] = element.value;

                /*fix for ie issue*/
//              this.elementLabel = element.selectedOptions[0].config.label;
                this.elementLabel = $(element).find('option:selected').text();

                //Check for the status of out of stock module
                if ($(".getOutOfStatus").val() == 1) {
                    this.outOfStock = this.elementLabel.indexOf("Out of Stock");
                    if(this.outOfStock < 0) {
                       $(".box-tocart").show();
                       $(".product-info-stock-sku .unavailable").hide();
                       $(".product-info-stock-sku .available").show();
                    } else {
                       $(".box-tocart").hide();
                       $(".product-info-stock-sku .available").hide();
                       $(".product-info-stock-sku .unavailable").show();
                       $(".productAlert").show();
                    }
                }
                if (element.nextSetting) {
                    element.nextSetting.disabled = false;
                    this._fillSelect(element.nextSetting);
                    this._resetChildren(element.nextSetting);
                } else {
                    if (!!document.documentMode) { //eslint-disable-line
                        this.inputSimpleProduct.val(element.options[element.selectedIndex].config.allowedProducts[0]);
                    } else {
                        this.inputSimpleProduct.val(element.selectedOptions[0].config.allowedProducts[0]);
                    }
                }
            } else {
                this._resetChildren(element);
            }

            this._reloadPrice();
            this._displayRegularPriceBlock(this.simpleProduct);
            this._displayTierPriceBlock(this.simpleProduct);
            this._displayNormalPriceLabel();
            this._changeProductImage();
        }
    });

    return $.silvan.configurable;
});