<?php
namespace Earthlite\Checkout\Plugin\Model\Product\Type;


/**
 * class ConfigurablePlugin
 */
class ConfigurablePlugin
{
    /**
     * 
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $subject
     * @param bool $result
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function afterIsSalable(
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $subject,
        $result,
        $product
    ) {
        return true;
    }
}