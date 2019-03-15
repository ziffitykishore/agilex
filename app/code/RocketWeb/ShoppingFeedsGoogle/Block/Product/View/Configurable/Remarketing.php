<?php

namespace RocketWeb\ShoppingFeedsGoogle\Block\Product\View\Configurable;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Remarketing extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{
    const GOOGLE_CONVERSION_ID_PATH = 'shoppingfeeds/google_remarketing/rw_google_remarketing_conversion_id';
    const REMARKETING_ENABLED_PATH = 'shoppingfeeds/google_remarketing/rw_google_remarketing_enable';

    /**
     * Product Type Configurable instance.
     *
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $productTypeConfigFactory = null;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param CurrentCustomer $currentCustomer
     * @param PriceCurrencyInterface $priceCurrency
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductType
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductType,
        array $data = []
    )
    {
        parent::__construct($context, $arrayUtils, $jsonEncoder, $helper,
            $catalogProduct, $currentCustomer, $priceCurrency, $configurableAttributeData, $data);
        $this->productTypeConfigFactory = $configurableProductType;
    }

    /**
     * Return Google Conversion Id.
     *
     * @return string
     */
    public function getGoogleConversionId()
    {
        return $this->_scopeConfig->getValue(self::GOOGLE_CONVERSION_ID_PATH);
    }

    /**
     * Returns if remarketing is enabled in admin config.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->_scopeConfig->getValue(self::REMARKETING_ENABLED_PATH);
    }

    /**
     * Returns array of children products.
     *
     * @return array
     */
    public function getProducts()
    {
        if ($this->hasData('products')) {
            return $this->getData('products');
        }

        $product = $this->getProduct();

        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $this->setData('products', $this->productTypeConfigFactory->getUsedProducts($product));
        }

        return $this->getData('products');
    }
}