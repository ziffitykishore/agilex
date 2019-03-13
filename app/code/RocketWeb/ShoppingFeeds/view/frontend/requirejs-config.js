var config = {
    map: {
        '*': {
            "autoSelectSimple": "RocketWeb_ShoppingFeeds/js/autoselect/simple",
            "autoSelectConfigurable": "RocketWeb_ShoppingFeeds/js/autoselect/configurable"
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'RocketWeb_ShoppingFeeds/js/swatch-renderer/mixin': true
            }
        }
    }
};