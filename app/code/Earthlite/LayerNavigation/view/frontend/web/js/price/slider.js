define(
    [
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Mageplaza_AjaxLayer/js/action/submit-filter',
    'jquery/ui',
    ], function($, priceUltil, submitFilterAction) {
        "use strict";
    
        $.widget(
            'custom.layerSlider', {
                options: {
                    sliderElement: '#custom_price_slider',
                    textElement: '#custom_price_text'
                },
                _create: function () {
                    var self = this;  
                    $("#apply_price").hide();          
                    $(this.options.sliderElement).slider(
                        {
                            min: self.options.priceMin,
                            max: self.options.priceMax,
                            values: [self.options.selectedFrom, self.options.selectedTo],
                            slide: function( event, ui ) {
                                $("#apply_price").show();
                                self.showText(ui.values[0], ui.values[1]);
                            },
                            change: function(event, ui) {                                 
                                $("#apply_price").show();
                                self.applyPriceRange();
                            }
                        }
                    );
                    this.applyPrice();
                    this.applyPriceRange();
                    this.showText(this.options.selectedFrom, this.options.selectedTo);
                },

                applyPriceRange: function(){
                    var leftValLen,rightValLen = 0;
                    var leftVal = $('#custom_price_slider .ui-slider-handle').get(0).style.left;
                    var rightVal = $('#custom_price_slider .ui-slider-handle').get(1).style.left;                    
                    leftValLen = leftVal.toString().replace('%', '');
                    rightValLen = rightVal.toString().replace('%', '');
                    var width = rightValLen - leftValLen;
                    var rangeWidth = width+'%';
                    $("#custom_price_slider #range_selected").css({"width": rangeWidth, "left": leftVal});
                },

                applyPrice: function(){
                    var self = this;
                    $("#apply_price").click(
                        function(e) {
                            var minVal = $("#custom_price_slider").slider("option", "values")[0];
                            var maxVal = $("#custom_price_slider").slider("option", "values")[1];
                            return submitFilterAction(self.getUrl(minVal, maxVal));                            
                        }
                    );
                },

                getUrl: function(from, to){                        
                    return this.options.ajaxUrl.replace(encodeURI('{price_start}'), from).replace(encodeURI('{price_end}'), to);
                },

                showText: function(from, to){
                    $(this.options.textElement).html("<div class='min-price'>" +this.formatPrice(from) + "</div><div class='max-price'>" + this.formatPrice(to) + '</div>');
                },

                formatPrice: function(value) {
                    return priceUltil.formatPrice(value, this.options.priceFormat);
                }
            }
        );

        return $.custom.layerSlider;
    }
);