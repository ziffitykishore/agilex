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
                    $(this.options.sliderElement).slider(
                        {
                            min: self.options.priceMin,
                            max: self.options.priceMax,
                            values: [self.options.selectedFrom, self.options.selectedTo],
                            slide: function( event, ui ) {                                
                                self.showText(ui.values[0], ui.values[1]);
                                self.applyPriceRange();
                            },
                            change: function(event, ui) {                                                                 
                                self.applyPriceRange();
                                submitFilterAction(self.getUrl(ui.values[0], ui.values[1]));
                            }
                        }
                    );                  
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