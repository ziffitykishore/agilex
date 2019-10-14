var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'PartySupplies_ConfigurableProduct/js/model/configurable-mixin': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'PartySupplies_ConfigurableProduct/js/model/swatch-renderer-mixin': true
            },
            'Magento_Catalog/js/price-box': {
                'PartySupplies_ConfigurableProduct/js/price-box-mixin': true
            }
        }
    }
};
