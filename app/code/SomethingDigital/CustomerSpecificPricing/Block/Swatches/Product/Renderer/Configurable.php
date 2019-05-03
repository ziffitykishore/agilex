<?php

namespace SomethingDigital\CustomerSpecificPricing\Block\Swatches\Product\Renderer;

use Magento\Swatches\Block\Product\Renderer\Configurable as BaseConfigurable;

/**
 * Swatch renderer block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurable extends BaseConfigurable
{
    /**
     * Path to template file with Swatch renderer.
     */
    const SWATCH_RENDERER_TEMPLATE = 'SomethingDigital_CustomerSpecificPricing::product/view/renderer.phtml';

    /**
     * Path to default template file with standard Configurable renderer.
     */
    const CONFIGURABLE_RENDERER_TEMPLATE = 'Magento_ConfigurableProduct::product/view/type/options/configurable.phtml';

    /**
     * @codeCoverageIgnore
     * @return string
     */
    protected function getRendererTemplate()
    {
        return $this->isProductHasSwatchAttribute() ?
            static::SWATCH_RENDERER_TEMPLATE : static::CONFIGURABLE_RENDERER_TEMPLATE;
    }
}

